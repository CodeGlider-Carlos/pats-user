<?php

namespace App\Services\Prosa\Checkout;

use App\Models\ProsaPendingCheckout;
use App\Models\ProsaToken;
use App\Models\ProsaTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Completer del portal de pagos (/pagos/procesar): actualiza o crea el
 * pasaporte PATS, registra el pago y genera comisiones.
 *
 * Se ejecuta tanto en el camino frictionless (inline) como al regresar del
 * reto 3DS. El contexto necesario viene del ProsaPendingCheckout::payload.
 */
class PagosRenovacionCheckout implements CheckoutCompleter
{
    public const FLOW = 'pagos_renovacion';

    public function flow(): string
    {
        return self::FLOW;
    }

    public function complete(ProsaPendingCheckout $checkout, array $paymentResult): string
    {
        $p      = $checkout->payload ?? [];
        $ahora  = Carbon::now();

        $paymentId = $paymentResult['paymentId'];
        $cardBrand = $paymentResult['brand'] ?: ($p['card_brand'] ?? 'CARD');
        $cardLast4 = $paymentResult['last4'] ?? ($p['card_last4'] ?? '????');
        $frecuencia   = strtoupper($p['frecuencia'] ?? 'MENSUAL');
        $meses        = max(1, (int) ($p['meses'] ?? 1));
        $monto        = (float) ($p['monto_orden'] ?? 0);
        $montoMembresia = (float) ($p['monto_membresia'] ?? $monto);
        $operacion    = $p['operacion'] ?? 'RENOVACION_PATS';
        $referencia   = $p['referencia'] ?? $paymentId;
        $idOrden      = $p['id_orden'] ?? null;

        // Idempotencia: si la orden ya quedó pagada no repetir.
        if ($idOrden) {
            $orden = DB::table('pats_ordenes_pago')->where('id_orden', $idOrden)->first();
            if ($orden && $orden->estatus_pago === 'CONFIRMADO') {
                return route('pagos') . '?pago=ok';
            }
        }

        // Buscar usuario y pasaporte.
        $user = DB::table('pats_users')->find($checkout->user_id);

        if (! $user) {
            return route('pagos') . '?pago=error';
        }

        $pasaporte = DB::table('pats_pasaportes')
            ->where('correo', $user->correo_usuario)
            ->where('activo', 1)
            ->orderBy('fecha_alta', 'desc')
            ->first();

        DB::transaction(function () use (
            $p, $ahora, $paymentId, $cardBrand, $cardLast4, $frecuencia,
            $meses, $monto, $montoMembresia, $operacion, $referencia,
            $idOrden, $user, $pasaporte, $checkout
        ) {
            $vigencia = $frecuencia === 'ANUAL'
                ? $ahora->copy()->addYear()->toDateString()
                : $ahora->copy()->addMonths($meses)->toDateString();
            $vencReal = $frecuencia === 'ANUAL'
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
                    'meses_vencidos'         => 0,
                    'recargo_acumulado'      => 0.00,
                    'updated_at'             => $ahora,
                ]);
                $idPasaporte = $pasaporte->id_pasaporte;
            } else {
                $idPasaporte = DB::table('pats_pasaportes')->insertGetId([
                    'id_franquicia'          => 1,
                    'id_distribuidor'        => 1,
                    'id_tipo_precio'         => $p['id_tipo_precio'] ?? 2,
                    'curp'                   => strtoupper($p['curp'] ?? ''),
                    'nombres'                => $p['nombres'] ?? $user->nombre,
                    'apellido_pa'            => $p['apellido_pa'] ?? '',
                    'apellido_ma'            => $p['apellido_ma'] ?? '',
                    'fecha_nacimiento'       => $p['fecha_nacimiento'] ?? null,
                    'telefono'               => $user->telefono ?? '',
                    'correo'                 => $user->correo_usuario,
                    'fecha_alta'             => $ahora,
                    'vigencia'               => $vigencia,
                    'frecuencia_pago'        => $frecuencia,
                    'estatus'                => 'activo',
                    'valor_pasaporte'        => $monto,
                    'valor_final_pasaporte'  => $monto,
                    'pais'                   => 'México',
                    'fecha_ultimo_pago'      => $ahora,
                    'fecha_vencimiento_real' => $vencReal,
                    'meses_vencidos'         => 0,
                    'recargo_acumulado'      => 0.00,
                    'activo'                 => 1,
                    'created_at'             => $ahora,
                    'updated_at'             => $ahora,
                ]);
            }

            if ($idOrden) {
                DB::table('pats_ordenes_pago')->where('id_orden', $idOrden)->update([
                    'id_pasaporte'                    => $idPasaporte,
                    'estatus_orden'                   => 'PAGADA',
                    'estatus_pago'                    => 'CONFIRMADO',
                    'transaccion_id_externa'          => (string) $paymentId,
                    'payment_intent_id'               => $paymentId,
                    'pasaporte_creado'                => 1,
                    'id_pasaporte_generado'           => $idPasaporte,
                    'fecha_alta_pasaporte'            => $ahora,
                    'procesado_integracion'           => 1,
                    'fecha_procesamiento_integracion' => $ahora,
                    'intentos_procesamiento'          => 1,
                    'fecha_pago'                      => $ahora,
                    'fecha_confirmacion'              => $ahora,
                    'user_confirmo'                   => $user->correo_usuario,
                    'updated_at'                      => $ahora,
                ]);
            }

            DB::table('pats_pagos_pasaporte')->insert([
                'id_orden'               => $idOrden,
                'id_pasaporte'           => $idPasaporte,
                'id_franquicia'          => $pasaporte->id_franquicia ?? 1,
                'id_distribuidor'        => $pasaporte->id_distribuidor ?? 1,
                'id_tipo_precio'         => $p['id_tipo_precio'] ?? 2,
                'correo'                 => $user->correo_usuario,
                'curp'                   => strtoupper($p['curp'] ?? ''),
                'nombre_usuario'         => $p['nombres'] ?? $user->nombre,
                'apellido_pa'            => $p['apellido_pa'] ?? '',
                'apellido_ma'            => $p['apellido_ma'] ?? null,
                'tipo_operacion'         => $p['operacion'] ?? 'RENOVACION_PATS',
                'monto'                  => $monto,
                'monto_nominal_base'     => $montoMembresia,
                'monto_extra_recargo'    => (float) ($p['recargo'] ?? 0),
                'frecuencia'             => strtolower($frecuencia),
                'metodo_pago'            => 'tarjeta_' . $cardBrand,
                'referencia_pago'        => $referencia,
                'referencia_externa'     => $paymentId,
                'transaccion_id_externa' => (string) $paymentId,
                'proveedor_pasarela'     => 'PROSA',
                'estatus_pago'           => 'confirmado',
                'fecha_pago'             => $ahora,
                'fecha_confirmacion'     => $ahora,
                'moneda'                 => 'MXN',
                'observaciones'          => "Prosa Pago:{$paymentId} {$cardBrand}···{$cardLast4}",
                'created_at'             => $ahora,
                'updated_at'             => $ahora,
            ]);

            // ── Comisiones ────────────────────────────────────────────
            $reglas = DB::table('pats_reglas_comision')
                ->where('tipo_operacion', 'pasaporte')
                ->where('subtipo_operacion', 'membresia')
                ->where('modalidad_pago', strtolower($frecuencia))
                ->where('activo', 1)
                ->whereNull('vigencia_fin')
                ->get();

            foreach ($reglas as $regla) {
                $mc    = $regla->tipo_calculo === 'monto_fijo'
                    ? (float) $regla->valor_calculo
                    : round($monto * (float) $regla->valor_calculo / 100, 2);
                $tipo  = match ($regla->beneficiario) { 'admin' => 'corpo', 'unidad' => 'unidad', 'franquicia' => 'franquicia', 'distribuidor' => 'distribuidor', default => 'corpo' };
                $idRel = match ($regla->beneficiario) { 'franquicia' => $pasaporte->id_franquicia ?? 1, 'distribuidor' => $pasaporte->id_distribuidor ?? 1, default => 1 };

                if (in_array($regla->beneficiario, ['franquicia', 'distribuidor'])) {
                    DB::table('pats_comisiones_generadas')->insert([
                        'tipo_origen' => 'pago_pasaporte', 'id_origen' => $idOrden ?? 0,
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
                    'estatus' => in_array($regla->beneficiario, ['admin', 'unidad']) ? 'pagado' : 'pendiente',
                    'fecha_generado' => $ahora, 'moneda' => 'MXN',
                    'observaciones' => "Prosa Pago:{$paymentId} | {$regla->beneficiario}",
                    'origen_tabla' => 'pats_ordenes_pago', 'origen_id' => $idOrden ?? 0,
                    'created_at' => $ahora, 'updated_at' => $ahora,
                ]);
            }

            // ── ProsaTransaction ──────────────────────────────────────
            ProsaTransaction::updateOrCreate(
                ['payment_id' => $paymentId],
                [
                    'user_id'        => $checkout->user_id,
                    'registration_id' => $paymentResult['registrationId'] ?? null,
                    'payment_type'   => 'DB',
                    'amount'         => $monto,
                    'currency'       => config('prosa.currency'),
                    'result_code'    => $paymentResult['resultCode'] ?? null,
                    'brand'          => $cardBrand,
                    'last4'          => $cardLast4,
                    'status'         => 'approved',
                    'origen'         => 'pagos_renovacion',
                ],
            );

            // ── Guardar tarjeta ───────────────────────────────────────
            $registrationId = $paymentResult['registrationId'] ?? null;
            if (! empty($p['saveCard']) && $registrationId && $checkout->user_id) {
                $alreadySaved = ProsaToken::where('registration_id', $registrationId)->exists();
                if (! $alreadySaved) {
                    $token = ProsaToken::create([
                        'user_id'         => $checkout->user_id,
                        'registration_id' => $registrationId,
                        'alias'           => $p['alias'] ?? null,
                        'card_brand'      => $cardBrand,
                        'card_last4'      => $cardLast4,
                        'card_bin'        => $paymentResult['bin'] ?? null,
                        'cardholder_name' => $p['holder'] ?? null,
                        'exp_month'       => $p['expMonth'] ?? null,
                        'exp_year'        => $p['expYear'] ?? null,
                        'status'          => 'active',
                        'is_default'      => false,
                    ]);
                    if (ProsaToken::active()->forUser($checkout->user_id)->count() === 1) {
                        $token->setAsDefault();
                    }
                }
            }
        });

        return route('pagos') . '?pago=ok';
    }

    public function failUrl(ProsaPendingCheckout $checkout, string $reason): string
    {
        $idOrden = $checkout->payload['id_orden'] ?? null;

        if ($idOrden) {
            DB::table('pats_ordenes_pago')->where('id_orden', $idOrden)->update([
                'estatus_orden'     => 'FALLIDA',
                'estatus_pago'      => 'RECHAZADO',
                'error_integracion' => substr($reason, 0, 255),
                'updated_at'        => now(),
            ]);
        }

        return route('pagos') . '?pago=error';
    }
}
