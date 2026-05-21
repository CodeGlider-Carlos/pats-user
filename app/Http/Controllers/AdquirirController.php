<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

use App\Models\PatsUser;
use App\Services\Feenicia\OneStepSaleService;
use App\DTO\Feenicia\OneStepSaleData;
use App\Exceptions\Feenicia\FeeniciaException;
use App\Exceptions\Feenicia\FeeniciaTimeoutException;
use App\Services\Feenicia\ReversalService;

class AdquirirController extends Controller
{
    public function __construct(
        private readonly OneStepSaleService $oneStepSaleService,
        private readonly ReversalService    $reversalService,
    ) {}

    // ──────────────────────────────────────────────
    //  GET /adquirir?t={token}
    // ──────────────────────────────────────────────

    public function show(Request $request)
    {
        $token = trim($request->query('t', ''));

        if (empty($token)) {
            abort(400, 'Falta el token público.');
        }

        $ctx = $this->resolverToken($token);

        if (!$ctx) {
            abort(404, 'El link no es válido o ya no está activo.');
        }

        return view('adquirir.index', [
            'token' => $token,
            'ctx'   => $ctx,
            'precios' => [
                'mensual' => ['monto' => 800,  'id_tipo_precio' => 2],
                'anual'   => ['monto' => 9600, 'id_tipo_precio' => 1],
            ],
        ]);
    }

    // ──────────────────────────────────────────────
    //  POST /adquirir/procesar
    // ──────────────────────────────────────────────

    public function procesar(Request $request): JsonResponse
    {
        $request->validate([
            // Cuenta nueva
            'correo'          => ['required', 'email'],
            'password'        => ['required', 'string', 'min:8', 'confirmed'],
            'telefono_usuario'=> ['required', 'string', 'max:15'],

            // Datos personales
            'nombre_usuario'  => ['required', 'string'],
            'apellido_pa'     => ['required', 'string'],
            'apellido_ma'     => ['nullable', 'string'],
            'curp_usuario'    => ['required', 'string', 'max:18'],
            'fecha_nacimiento'=> ['required', 'date'],
            'tipo_cliente'    => ['required', 'in:privado,empresa'],

            // Domicilio
            'dom_calle'       => ['required', 'string'],
            'dom_num_ext'     => ['required', 'string'],
            'dom_colonia'     => ['required', 'string'],
            'dom_cp'          => ['required', 'string'],
            'dom_municipio'   => ['required', 'string'],
            'dom_estado'      => ['required', 'string'],
            'dom_pais'        => ['required', 'string'],

            // Plan
            'frecuencia'      => ['required', 'in:MENSUAL,ANUAL'],
            'monto_orden'     => ['required', 'numeric', 'min:1'],
            'id_tipo_precio'  => ['required', 'integer'],

            // Tarjeta
            'pan'             => ['required', 'string'],
            'cardholderName'  => ['required', 'string'],
            'cvv2'            => ['required', 'string', 'min:3', 'max:4'],
            'expDate'         => ['required', 'string', 'size:4'],

            // Token del distribuidor
            'token_publico'   => ['required', 'string'],
        ]);

        // ── Resolver token ─────────────────────────────────────
        $ctx = $this->resolverToken($request->token_publico);
        if (!$ctx) {
            return response()->json(['success' => false, 'error' => 'Token inválido.'], 400);
        }

        // ── Verificar que el correo no exista ya ───────────────
        $correo = strtolower(trim($request->correo));
        if (DB::table('pats_users')->where('correo', $correo)->exists()) {
            return response()->json([
                'success' => false,
                'error'   => 'Ya existe una cuenta con ese correo. Por favor inicia sesión.',
                'code'    => 'EMAIL_EXISTS',
            ], 422);
        }

        $ahora      = Carbon::now();
        $referencia = 'PATS-' . $ahora->format('YmdHis') . '-' . strtoupper(substr(md5(uniqid()), 0, 8));
        $folio      = 'ORD-'  . $ahora->format('Ymd')    . '-' . strtoupper(substr(md5(uniqid()), 0, 6));

        // ── 1. Orden PENDIENTE ─────────────────────────────────
        $idOrden = DB::table('pats_ordenes_pago')->insertGetId([
            'folio_orden'         => $folio,
            'referencia_pago'     => $referencia,
            'tipo_origen'         => $ctx['tipo_origen'],
            'origen_checkout'     => 'PORTAL_PUBLICO',
            'id_distribuidor'     => $ctx['id_distribuidor'],
            'id_franquicia'       => $ctx['id_franquicia'],
            'correo_usuario_pats' => $correo,
            'curp_usuario'        => strtoupper($request->curp_usuario),
            'nombre_usuario'      => $request->nombre_usuario,
            'apellido_pa'         => $request->apellido_pa,
            'apellido_ma'         => $request->apellido_ma,
            'fecha_nacimiento'    => $request->fecha_nacimiento,
            'telefono_usuario'    => $request->telefono_usuario,
            'id_tipo_precio'      => $request->id_tipo_precio,
            'tipo_operacion'      => 'ALTA_PATS',
            'frecuencia'          => $request->frecuencia,
            'monto_orden'         => $request->monto_orden,
            'monto_nominal_base'  => $request->monto_orden,
            'monto_extra_recargo' => 0.00,
            'moneda'              => 'MXN',
            'pais'                => $ctx['pais'],
            'region'              => $ctx['region'],
            'zona'                => $ctx['zona'],
            'unidad'              => $ctx['unidad'],
            'tipo_cliente'        => $request->tipo_cliente,
            'nombre_empresa'      => $request->nombre_empresa,
            'estatus_orden'       => 'PENDIENTE',
            'estatus_pago'        => 'PENDIENTE',
            'proveedor_pasarela'  => 'FEENICIA',
            'user_creo'           => $correo,
            'payload_checkout_json' => json_encode(['token_publico' => $request->token_publico, 'ctx' => $ctx]),
            'fecha_orden'         => $ahora,
            'created_at'          => $ahora,
            'updated_at'          => $ahora,
        ]);

        // ── 2. Cobrar con Feenicia ─────────────────────────────
        try {
            $resultado = $this->oneStepSaleService->execute(new OneStepSaleData(
                affiliation:     config('feenicia.affiliation'),
                amount:          (float) $request->monto_orden,
                transactionDate: (int) (microtime(true) * 1000),
                pan:             $request->pan,
                cardholderName:  $request->cardholderName,
                cvv2:            $request->cvv2,
                expDate:         $request->expDate,
                userId:          config('feenicia.user'),
                tip:             '0.0',
            ));

        } catch (FeeniciaTimeoutException $e) {
            DB::table('pats_ordenes_pago')->where('id_orden', $idOrden)->update([
                'estatus_orden'     => 'FALLIDA',
                'estatus_pago'      => 'TIMEOUT',
                'error_integracion' => 'Timeout',
                'updated_at'        => now(),
            ]);
            try { $this->reversalService->executeFromTimeout($e); } catch (\Throwable) {}
            return response()->json(['success' => false, 'error' => 'Timeout al procesar el pago.', 'code' => 'TIMEOUT'], 504);

        } catch (FeeniciaException $e) {
            DB::table('pats_ordenes_pago')->where('id_orden', $idOrden)->update([
                'estatus_orden'     => 'FALLIDA',
                'estatus_pago'      => 'RECHAZADO',
                'error_integracion' => $e->getMessage(),
                'updated_at'        => now(),
            ]);
            return response()->json(['success' => false, 'error' => $e->getMessage(), 'code' => $e->responseCode], 400);
        }

        // ── 3. Pago aprobado → pipeline completo ──────────────
        return DB::transaction(function () use (
            $request, $correo, $ctx, $resultado,
            $idOrden, $referencia, $folio, $ahora
        ) {
            $transactionId = $resultado['transactionId'];
            $authnum       = $resultado['authnum'];
            $cardBrand     = $resultado['card']['brand']       ?? 'CARD';
            $cardLast4     = $resultado['card']['last4Digits'] ?? '????';
            $frecuencia    = strtoupper($request->frecuencia);

            $vigencia        = $frecuencia === 'ANUAL'
                ? $ahora->copy()->addYear()->toDateString()
                : $ahora->copy()->addMonth()->toDateString();
            $vencimientoReal = $frecuencia === 'ANUAL'
                ? $ahora->copy()->addYear()->endOfDay()
                : $ahora->copy()->addMonth()->endOfDay();

            // ── 3a. Crear pasaporte ────────────────────────────
            $idPasaporte = DB::table('pats_pasaportes')->insertGetId([
                'id_franquicia'          => $ctx['id_franquicia'],
                'id_distribuidor'        => $ctx['id_distribuidor'],
                'id_tipo_precio'         => $request->id_tipo_precio,
                'curp'                   => strtoupper($request->curp_usuario),
                'nombres'                => $request->nombre_usuario,
                'apellido_pa'            => $request->apellido_pa,
                'apellido_ma'            => $request->apellido_ma ?? '',
                'fecha_nacimiento'       => $request->fecha_nacimiento,
                'telefono'               => $request->telefono_usuario,
                'correo'                 => $correo,
                'fecha_alta'             => $ahora,
                'vigencia'               => $vigencia,
                'frecuencia_pago'        => $frecuencia,
                'estatus'                => 'activo',
                'valor_pasaporte'        => $request->monto_orden,
                'valor_final_pasaporte'  => $request->monto_orden,
                'pais'                   => $ctx['pais'],
                'region'                 => $ctx['region'],
                'zona'                   => $ctx['zona'],
                'unidad'                 => $ctx['unidad'],
                'tipo_cliente'           => $request->tipo_cliente,
                'nombre_empresa'         => $request->nombre_empresa,
                'fecha_ultimo_pago'      => $ahora,
                'fecha_vencimiento_real' => $vencimientoReal,
                'meses_vencidos'         => 0,
                'recargo_acumulado'      => 0.00,
                'activo'                 => 1,
                'created_at'             => $ahora,
                'updated_at'             => $ahora,
            ]);

            // ── 3b. Crear usuario ──────────────────────────────
            $nombreCompleto = trim("{$request->nombre_usuario} {$request->apellido_pa} {$request->apellido_ma}");
            $idUsuario = DB::table('pats_users')->insertGetId([
                'app'                  => 'PATS',
                'rolapp'               => 'CLIENTEPATS',
                'rol'                  => 'CLIENTE',
                'tipo_actor'           => 'DISTRIBUIDOR',
                'id_actor'             => $ctx['id_distribuidor'],
                'nombre'               => $nombreCompleto,
                'usuario'              => $correo,
                'correo'               => $correo,
                'contrasena'           => Hash::make($request->password),
                'region'               => $ctx['region'],
                'acroregion'           => $ctx['region'],
                'unidad'               => $ctx['unidad'],
                'acronu'               => $ctx['unidad'],
                'activo'               => 1,
                'must_change_password' => 0,
                'telefono'             => $request->telefono_usuario,
                'created_at'           => $ahora,
                'updated_at'           => $ahora,
            ]);

            DB::table('pats_usuarios_scope')->insert([
                'user_id'         => $idUsuario,
                'rol_pats'        => 'CLIENTE',
                'pais'            => $ctx['pais'],
                'region'          => $ctx['region'],
                'zona'            => $ctx['zona'],
                'unidad'          => $ctx['unidad'],
                'id_distribuidor' => $ctx['id_distribuidor'],
                'id_franquicia'   => $ctx['id_franquicia'],
                'activo'          => 1,
                'created_at'      => $ahora,
                'updated_at'      => $ahora,
            ]);

            // ── 3c. Confirmar orden ────────────────────────────
            DB::table('pats_ordenes_pago')->where('id_orden', $idOrden)->update([
                'id_pasaporte'                    => $idPasaporte,
                'estatus_orden'                   => 'PAGADA',
                'estatus_pago'                    => 'CONFIRMADO',
                'transaccion_id_externa'          => (string) $transactionId,
                'payment_intent_id'               => $authnum,
                'usuario_creado'                  => 1,
                'id_usuario_generado'             => $idUsuario,
                'fecha_alta_usuario'              => $ahora,
                'pasaporte_creado'                => 1,
                'id_pasaporte_generado'           => $idPasaporte,
                'fecha_alta_pasaporte'            => $ahora,
                'procesado_integracion'           => 1,
                'fecha_procesamiento_integracion' => $ahora,
                'intentos_procesamiento'          => 1,
                'fecha_pago'                      => $ahora,
                'fecha_confirmacion'              => $ahora,
                'payload_confirmacion_json'       => json_encode($resultado),
                'user_confirmo'                   => $correo,
                'updated_at'                      => $ahora,
            ]);

            // ── 3d. Pago histórico ─────────────────────────────
            DB::table('pats_pagos_pasaporte')->insert([
                'id_orden'               => $idOrden,
                'id_pasaporte'           => $idPasaporte,
                'id_franquicia'          => $ctx['id_franquicia'],
                'id_distribuidor'        => $ctx['id_distribuidor'],
                'id_tipo_precio'         => $request->id_tipo_precio,
                'correo'                 => $correo,
                'curp'                   => strtoupper($request->curp_usuario),
                'nombre_usuario'         => $request->nombre_usuario,
                'apellido_pa'            => $request->apellido_pa,
                'apellido_ma'            => $request->apellido_ma,
                'tipo_operacion'         => 'ALTA_PATS',
                'monto'                  => $request->monto_orden,
                'monto_nominal_base'     => $request->monto_orden,
                'monto_extra_recargo'    => 0.00,
                'frecuencia'             => strtolower($request->frecuencia),
                'metodo_pago'            => 'tarjeta_' . $cardBrand,
                'referencia_pago'        => $referencia,
                'referencia_externa'     => $authnum,
                'transaccion_id_externa' => (string) $transactionId,
                'proveedor_pasarela'     => 'FEENICIA',
                'estatus_pago'           => 'confirmado',
                'response_json'          => json_encode($resultado),
                'fecha_pago'             => $ahora,
                'fecha_confirmacion'     => $ahora,
                'moneda'                 => 'MXN',
                'observaciones'          => "Feenicia Auth:{$authnum} {$cardBrand}···{$cardLast4}",
                'created_at'             => $ahora,
                'updated_at'             => $ahora,
            ]);

            // ── 3e. Comisiones ─────────────────────────────────
            $reglas = DB::table('pats_reglas_comision')
                ->where('tipo_operacion', 'pasaporte')
                ->where('subtipo_operacion', 'membresia')
                ->where('modalidad_pago', strtolower($request->frecuencia))
                ->where('activo', 1)
                ->whereNull('vigencia_fin')
                ->get();

            foreach ($reglas as $regla) {
                $mc = $regla->tipo_calculo === 'monto_fijo'
                    ? (float) $regla->valor_calculo
                    : round((float) $request->monto_orden * (float) $regla->valor_calculo / 100, 2);

                $tipo  = match($regla->beneficiario) { 'admin' => 'corpo', 'unidad' => 'unidad', 'franquicia' => 'franquicia', 'distribuidor' => 'distribuidor', default => 'corpo' };
                $idRel = match($regla->beneficiario) { 'franquicia' => $ctx['id_franquicia'], 'distribuidor' => $ctx['id_distribuidor'], default => 1 };

                if (in_array($regla->beneficiario, ['franquicia', 'distribuidor'])) {
                    DB::table('pats_comisiones_generadas')->insert([
                        'tipo_origen' => 'pago_pasaporte', 'id_origen' => $idOrden,
                        'id_regla' => $regla->id_regla, 'beneficiario_tipo' => $regla->beneficiario,
                        'beneficiario_id' => $idRel, 'monto_comision' => $mc,
                        'monto_aplicado_deuda' => 0, 'monto_liberado' => 0, 'moneda' => 'MXN',
                        'fecha_generacion' => $ahora, 'created_at' => $ahora, 'updated_at' => $ahora,
                    ]);
                }

                DB::table('pats_movimientos_financieros')->insert([
                    'tipo' => $tipo, 'id_relacionado' => $idRel, 'id_pasaporte' => $idPasaporte,
                    'monto' => $mc, 'tipo_movimiento' => "comision_pats_{$regla->beneficiario}",
                    'referencia' => $referencia,
                    'estatus' => in_array($regla->beneficiario, ['admin','unidad']) ? 'pagado' : 'pendiente',
                    'fecha_generado' => $ahora, 'moneda' => 'MXN',
                    'observaciones' => "Feenicia Auth:{$authnum} | {$regla->beneficiario}",
                    'origen_tabla' => 'pats_ordenes_pago', 'origen_id' => $idOrden,
                    'created_at' => $ahora, 'updated_at' => $ahora,
                ]);
            }

            // ── 4. Login automático ────────────────────────────
            $userModel = PatsUser::find($idUsuario);
            Auth::login($userModel);

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
                'redirect'      => route('pasaporte'),
            ]);
        });
    }

    // ──────────────────────────────────────────────
    //  Resolver token — busca en distribuidores y franquicias
    // ──────────────────────────────────────────────

    private function resolverToken(string $token): ?array
    {
        // Primero buscar en distribuidores
        $dist = DB::table('pats_distribuidores')
            ->where('public_checkout_token', $token)
            ->where('public_checkout_activo', 1)
            ->where('activo', 1)
            ->first();

        if ($dist) {
            return [
                'tipo_origen'    => 'DISTRIBUIDOR',
                'id_distribuidor'=> $dist->id_distribuidor,
                'id_franquicia'  => $dist->id_franquicia,
                'pais'           => $dist->pais ?? 'México',
                'region'         => $dist->region,
                'zona'           => $dist->zona,
                'unidad'         => $dist->unidad ?? 'PATS',
                'nombre'         => $dist->nombre,
            ];
        }

        // Luego en franquicias
        $franq = DB::table('pats_franquicias')
            ->where('public_checkout_token', $token)
            ->where('activo', 1)
            ->first();

        if ($franq) {
            return [
                'tipo_origen'    => 'FRANQUICIA',
                'id_distribuidor'=> 0,
                'id_franquicia'  => $franq->id_franquicia,
                'pais'           => $franq->pais ?? 'México',
                'region'         => $franq->region ?? '',
                'zona'           => $franq->zona ?? '',
                'unidad'         => $franq->unidad ?? 'PATS',
                'nombre'         => $franq->nombre,
            ];
        }

        return null;
    }
}