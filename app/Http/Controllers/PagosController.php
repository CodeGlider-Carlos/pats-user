<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Services\Feenicia\OneStepSaleService;
use App\DTO\Feenicia\OneStepSaleData;
use App\Exceptions\Feenicia\FeeniciaException;
use App\Exceptions\Feenicia\FeeniciaTimeoutException;
use App\Services\Feenicia\ReversalService;

class PagosController extends Controller
{
    public function __construct(
        private readonly OneStepSaleService $oneStepSaleService,
        private readonly ReversalService    $reversalService,
    ) {}

    public function index()
    {
        $user = auth('pasaporte')->user();

        // Pasaporte activo del usuario logueado
        $pasaporte = DB::table('pats_pasaportes')
            ->where('correo', $user->correo_usuario)
            ->where('activo', 1)
            ->orderBy('fecha_alta', 'desc')
            ->first();

        // Historial de pagos desde pats_pagos_pasaporte
        $pagosRaw = DB::table('pats_pagos_pasaporte')
            ->where('correo', $user->correo_usuario)
            ->orderBy('fecha_pago', 'desc')
            ->get();

        $pagos = $pagosRaw->map(fn($p) => [
            'folio'    => $p->referencia_pago ?? 'PATS-' . str_pad($p->id, 4, '0', STR_PAD_LEFT),
            'producto' => match (strtoupper($p->tipo_operacion ?? '')) {
                'ALTA_PATS'       => 'Alta Pasaporte PATS',
                'RENOVACION_PATS' => 'Renovación Pasaporte PATS',
                default           => 'Membresía PATS',
            },
            'fecha'   => $p->fecha_pago ? Carbon::parse($p->fecha_pago)->format('d/m/Y') : '—',
            'monto'   => (float) $p->monto,
            'metodo'  => ucwords(str_replace('_', ' ', $p->metodo_pago ?? $p->proveedor_pasarela ?? 'Tarjeta')),
            'estatus' => match (strtolower($p->estatus_pago ?? '')) {
                'confirmado' => 'Pagado',
                'pendiente'  => 'Pendiente',
                'rechazado'  => 'Rechazado',
                default      => ucfirst($p->estatus_pago ?? 'Pagado'),
            },
            'frecuencia' => ucfirst(strtolower($p->frecuencia ?? '')),
            'authnum'    => $p->referencia_externa ?? null,
            'proveedor'  => $p->proveedor_pasarela ?? null,
        ]);

        return view('feenicia.payment', [
            'user'        => $user,
            'pasaporte'   => $pasaporte,
            'pagos'       => $pagos,
            'totalPagos'  => $pagos->count(),
            'totalPagado' => $pagos->sum('monto'),
            'ultimoPago'  => $pagos->first()['fecha'] ?? '—',
        ]);
    }

    public function procesar(Request $request): JsonResponse
    {
        $request->validate([
            'nombre_usuario'  => ['required', 'string'],
            'apellido_pa'     => ['required', 'string'],
            'apellido_ma'     => ['nullable', 'string'],
            'curp_usuario'    => ['required', 'string', 'max:18'],
            'fecha_nacimiento' => ['required', 'date'],
            'telefono_usuario' => ['required', 'string'],
            'tipo_cliente'    => ['required', 'in:privado,empresa'],
            'dom_calle'       => ['required', 'string'],
            'dom_num_ext'     => ['required', 'string'],
            'dom_colonia'     => ['required', 'string'],
            'dom_cp'          => ['required', 'string'],
            'dom_municipio'   => ['required', 'string'],
            'dom_estado'      => ['required', 'string'],
            'dom_pais'        => ['required', 'string'],
            'frecuencia'      => ['required', 'in:MENSUAL,ANUAL'],
            'monto_orden'     => ['required', 'numeric', 'min:1'],
            'id_tipo_precio'  => ['required', 'integer'],
            'pan'             => ['required', 'string'],
            'cardholderName'  => ['required', 'string'],
            'cvv2'            => ['required', 'string', 'min:3', 'max:4'],
            'expDate'         => ['required', 'string', 'size:4'],
        ]);

        $user       = auth()->user();
        $ahora      = Carbon::now();
        $referencia = 'PATS-' . $ahora->format('YmdHis') . '-' . strtoupper(substr(md5(uniqid()), 0, 8));
        $folio      = 'ORD-'  . $ahora->format('Ymd')    . '-' . strtoupper(substr(md5(uniqid()), 0, 6));

        $pasaporte = DB::table('pats_pasaportes')
            ->where('correo', $user->correo_usuario)
            ->where('activo', 1)
            ->orderBy('fecha_alta', 'desc')
            ->first();

        $operacion = $pasaporte ? 'RENOVACION_PATS' : 'ALTA_PATS';

        $idOrden = DB::table('pats_ordenes_pago')->insertGetId([
            'folio_orden'         => $folio,
            'referencia_pago'     => $referencia,
            'tipo_origen'         => 'PORTAL_CLIENTE',
            'origen_checkout'     => 'PORTAL_CLIENTE',
            'id_distribuidor'     => $pasaporte->id_distribuidor ?? 1,
            'id_franquicia'       => $pasaporte->id_franquicia   ?? 1,
            'id_pasaporte'        => $pasaporte->id_pasaporte    ?? null,
            'correo_usuario_pats' => $user->correo_usuario,
            'curp_usuario'        => strtoupper($request->curp_usuario),
            'nombre_usuario'      => $request->nombre_usuario,
            'apellido_pa'         => $request->apellido_pa,
            'apellido_ma'         => $request->apellido_ma,
            'fecha_nacimiento'    => $request->fecha_nacimiento,
            'telefono_usuario'    => $request->telefono_usuario,
            'id_tipo_precio'      => $request->id_tipo_precio,
            'tipo_operacion'      => $operacion,
            'frecuencia'          => $request->frecuencia,
            'monto_orden'         => $request->monto_orden,
            'monto_nominal_base'  => $request->monto_orden,
            'monto_extra_recargo' => 0.00,
            'moneda'              => 'MXN',
            'pais'                => $request->dom_pais,
            'region'              => $request->dom_estado,
            'zona'                => $request->dom_municipio,
            'tipo_cliente'        => $request->tipo_cliente,
            'nombre_empresa'      => $request->nombre_empresa,
            'estatus_orden'       => 'PENDIENTE',
            'estatus_pago'        => 'PENDIENTE',
            'proveedor_pasarela'  => 'FEENICIA',
            'user_creo'           => $user->correo_usuario,
            'fecha_orden'         => $ahora,
            'created_at'          => $ahora,
            'updated_at'          => $ahora,
        ]);

        try {
            $resultado = $this->oneStepSaleService->execute(new OneStepSaleData(
                affiliation: config('feenicia.affiliation'),
                amount: (float) $request->monto_orden,
                transactionDate: (int) (microtime(true) * 1000),
                pan: $request->pan,
                cardholderName: $request->cardholderName,
                cvv2: $request->cvv2,
                expDate: $request->expDate,
                userId: config('feenicia.user'),
                tip: '0.0',
            ));
        } catch (FeeniciaTimeoutException $e) {
            DB::table('pats_ordenes_pago')->where('id_orden', $idOrden)->update([
                'estatus_orden' => 'FALLIDA',
                'estatus_pago' => 'TIMEOUT',
                'error_integracion' => 'Timeout',
                'updated_at' => now(),
            ]);
            try {
                $this->reversalService->executeFromTimeout($e);
            } catch (\Throwable) {
            }
            return response()->json(['success' => false, 'error' => 'Timeout al procesar el pago.', 'code' => 'TIMEOUT'], 504);
        } catch (FeeniciaException $e) {
            DB::table('pats_ordenes_pago')->where('id_orden', $idOrden)->update([
                'estatus_orden' => 'FALLIDA',
                'estatus_pago' => 'RECHAZADO',
                'error_integracion' => $e->getMessage(),
                'updated_at' => now(),
            ]);
            return response()->json(['success' => false, 'error' => $e->getMessage(), 'code' => $e->responseCode], 400);
        }

        return DB::transaction(function () use ($request, $user, $pasaporte, $resultado, $idOrden, $referencia, $folio, $ahora, $operacion) {

            $transactionId = $resultado['transactionId'];
            $authnum       = $resultado['authnum'];
            $cardBrand     = $resultado['card']['brand']       ?? 'CARD';
            $cardLast4     = $resultado['card']['last4Digits'] ?? '????';
            $frecuencia    = strtoupper($request->frecuencia);
            $meses         = max(1, (int) ($request->input('_pats_meses', 1)));
            $montoMembresia = (float) ($request->input('_pats_monto_membresia', $request->monto_orden));
            $montoRecargo   = (float) ($request->input('_pats_recargo', 0));
            $vigencia      = $frecuencia === 'ANUAL'
                ? $ahora->copy()->addYear()->toDateString()
                : $ahora->copy()->addMonths($meses)->toDateString();
            $vencReal      = $frecuencia === 'ANUAL'
                ? $ahora->copy()->addYear()->endOfDay()
                : $ahora->copy()->addMonths($meses)->endOfDay();

            if ($pasaporte) {
                DB::table('pats_pasaportes')->where('id_pasaporte', $pasaporte->id_pasaporte)->update([
                    'vigencia'               => $vigencia,
                    'frecuencia_pago'        => $frecuencia,
                    'estatus'                => 'activo',
                    'valor_pasaporte'        => $montoMembresia,
                    'valor_final_pasaporte'  => $montoMembresia,
                    'fecha_ultimo_pago'      => $ahora,
                    'fecha_vencimiento_real' => $vencReal,
                    'meses_vencidos'         => 0,      // resetear meses vencidos
                    'recargo_acumulado'      => 0.00,   // resetear recargo tras pago
                    'updated_at'             => $ahora,
                ]);
                $idPasaporte = $pasaporte->id_pasaporte;
            } else {
                $idPasaporte = DB::table('pats_pasaportes')->insertGetId([
                    'id_franquicia' => 1,
                    'id_distribuidor' => 1,
                    'id_tipo_precio' => $request->id_tipo_precio,
                    'curp' => strtoupper($request->curp_usuario),
                    'nombres' => $request->nombre_usuario,
                    'apellido_pa' => $request->apellido_pa,
                    'apellido_ma' => $request->apellido_ma ?? '',
                    'fecha_nacimiento' => $request->fecha_nacimiento,
                    'telefono' => $request->telefono_usuario,
                    'correo' => $user->correo_usuario,
                    'fecha_alta' => $ahora,
                    'vigencia' => $vigencia,
                    'frecuencia_pago' => $frecuencia,
                    'estatus' => 'activo',
                    'valor_pasaporte' => $request->monto_orden,
                    'valor_final_pasaporte' => $request->monto_orden,
                    'pais' => $request->dom_pais,
                    'region' => $request->dom_estado,
                    'zona' => $request->dom_municipio,
                    'tipo_cliente' => $request->tipo_cliente,
                    'nombre_empresa' => $request->nombre_empresa,
                    'fecha_ultimo_pago' => $ahora,
                    'fecha_vencimiento_real' => $vencReal,
                    'meses_vencidos' => 0,
                    'recargo_acumulado' => 0.00,
                    'activo' => 1,
                    'created_at' => $ahora,
                    'updated_at' => $ahora,
                ]);
            }

            DB::table('pats_ordenes_pago')->where('id_orden', $idOrden)->update([
                'id_pasaporte' => $idPasaporte,
                'estatus_orden' => 'PAGADA',
                'estatus_pago' => 'CONFIRMADO',
                'transaccion_id_externa' => (string) $transactionId,
                'payment_intent_id' => $authnum,
                'pasaporte_creado' => 1,
                'id_pasaporte_generado' => $idPasaporte,
                'fecha_alta_pasaporte' => $ahora,
                'procesado_integracion' => 1,
                'fecha_procesamiento_integracion' => $ahora,
                'intentos_procesamiento' => 1,
                'fecha_pago' => $ahora,
                'fecha_confirmacion' => $ahora,
                'payload_confirmacion_json' => json_encode($resultado),
                'user_confirmo' => $user->correo_usuario,
                'updated_at' => $ahora,
            ]);

            DB::table('pats_pagos_pasaporte')->insert([
                'id_orden' => $idOrden,
                'id_pasaporte' => $idPasaporte,
                'id_franquicia' => $pasaporte->id_franquicia ?? 1,
                'id_distribuidor' => $pasaporte->id_distribuidor ?? 1,
                'id_tipo_precio' => $request->id_tipo_precio,
                'correo' => $user->correo_usuario,
                'curp' => strtoupper($request->curp_usuario),
                'nombre_usuario' => $request->nombre_usuario,
                'apellido_pa' => $request->apellido_pa,
                'apellido_ma' => $request->apellido_ma,
                'tipo_operacion' => $operacion,
                'monto' => $request->monto_orden,
                'monto_nominal_base' => $request->monto_orden,
                'monto_extra_recargo' => 0.00,
                'frecuencia' => strtolower($request->frecuencia),
                'metodo_pago' => 'tarjeta_' . $cardBrand,
                'referencia_pago' => $referencia,
                'referencia_externa' => $authnum,
                'transaccion_id_externa' => (string) $transactionId,
                'proveedor_pasarela' => 'FEENICIA',
                'estatus_pago' => 'confirmado',
                'response_json' => json_encode($resultado),
                'fecha_pago' => $ahora,
                'fecha_confirmacion' => $ahora,
                'moneda' => 'MXN',
                'observaciones' => "Feenicia Auth:{$authnum} {$cardBrand}···{$cardLast4}",
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);

            $reglas = DB::table('pats_reglas_comision')
                ->where('tipo_operacion', 'pasaporte')->where('subtipo_operacion', 'membresia')
                ->where('modalidad_pago', strtolower($request->frecuencia))
                ->where('activo', 1)->whereNull('vigencia_fin')->get();

            foreach ($reglas as $regla) {
                $mc    = $regla->tipo_calculo === 'monto_fijo' ? (float) $regla->valor_calculo : round((float) $request->monto_orden * (float) $regla->valor_calculo / 100, 2);
                $tipo  = match ($regla->beneficiario) {
                    'admin' => 'corpo',
                    'unidad' => 'unidad',
                    'franquicia' => 'franquicia',
                    'distribuidor' => 'distribuidor',
                    default => 'corpo'
                };
                $idRel = match ($regla->beneficiario) {
                    'franquicia' => $pasaporte->id_franquicia ?? 1,
                    'distribuidor' => $pasaporte->id_distribuidor ?? 1,
                    default => 1
                };

                if (in_array($regla->beneficiario, ['franquicia', 'distribuidor'])) {
                    DB::table('pats_comisiones_generadas')->insert([
                        'tipo_origen' => 'pago_pasaporte',
                        'id_origen' => $idOrden,
                        'id_regla' => $regla->id_regla,
                        'beneficiario_tipo' => $regla->beneficiario,
                        'beneficiario_id' => $idRel,
                        'monto_comision' => $mc,
                        'monto_aplicado_deuda' => 0,
                        'monto_liberado' => 0,
                        'moneda' => 'MXN',
                        'fecha_generacion' => $ahora,
                        'created_at' => $ahora,
                        'updated_at' => $ahora,
                    ]);
                }
                DB::table('pats_movimientos_financieros')->insert([
                    'tipo' => $tipo,
                    'id_relacionado' => $idRel,
                    'id_pasaporte' => $idPasaporte,
                    'monto' => $mc,
                    'tipo_movimiento' => "comision_pats_{$regla->beneficiario}",
                    'referencia' => $referencia,
                    'estatus' => in_array($regla->beneficiario, ['admin', 'unidad']) ? 'pagado' : 'pendiente',
                    'fecha_generado' => $ahora,
                    'moneda' => 'MXN',
                    'observaciones' => "Feenicia Auth:{$authnum} | {$regla->beneficiario}",
                    'origen_tabla' => 'pats_ordenes_pago',
                    'origen_id' => $idOrden,
                    'created_at' => $ahora,
                    'updated_at' => $ahora,
                ]);
            }

            return response()->json([
                'success'       => true,
                'transactionId' => $transactionId,
                'authnum'       => $authnum,
                'referencia'    => $referencia,
                'folio'         => $folio,
                'idPasaporte'   => $idPasaporte,
                'card'          => ['brand' => $cardBrand, 'last4' => $cardLast4],
                'monto'         => $request->monto_orden,
                'vigencia'      => $vigencia,
                'operacion'     => $operacion,
            ]);
        });
    }
}
