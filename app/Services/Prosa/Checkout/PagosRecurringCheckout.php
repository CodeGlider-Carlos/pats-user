<?php

namespace App\Services\Prosa\Checkout;

use App\Models\ProsaPendingCheckout;
use App\Models\ProsaSubscription;
use App\Models\ProsaTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Completer para el cobro inicial de una suscripción recurrente.
 *
 * Hace todo lo que hace {@see PagosRenovacionCheckout} (actualiza el pasaporte,
 * registra el pago, genera comisiones) y adicionalmente crea el registro en
 * `prosa_subscriptions` para que el comando `prosa:charge-recurring` pueda
 * ejecutar los cobros subsecuentes automáticamente.
 */
class PagosRecurringCheckout implements CheckoutCompleter
{
    public const FLOW = 'pagos_recurrente';

    public function flow(): string
    {
        return self::FLOW;
    }

    public function complete(ProsaPendingCheckout $checkout, array $paymentResult): string
    {
        $p      = $checkout->payload ?? [];
        $ahora  = Carbon::now();

        $paymentId      = $paymentResult['paymentId'];
        $registrationId = $paymentResult['registrationId'] ?? null;
        $cardBrand      = $paymentResult['brand'] ?: ($p['card_brand'] ?? 'CARD');
        $cardLast4      = $paymentResult['last4']  ?? ($p['card_last4'] ?? '????');
        $frecuencia     = strtoupper($p['frecuencia']      ?? 'MENSUAL');
        $meses          = max(1, (int) ($p['meses']        ?? 1));
        $monto          = (float) ($p['monto_orden']       ?? 0);
        $montoMembresia = (float) ($p['monto_membresia']   ?? $monto);
        $operacion      = $p['operacion']                  ?? 'RENOVACION_PATS';
        $referencia     = $p['referencia']                 ?? $paymentId;
        $idOrden        = $p['id_orden']                   ?? null;
        $correo         = $p['correo_usuario']             ?? '';

        // ── Idempotencia ──────────────────────────────────────────────────────
        if ($idOrden) {
            $orden = DB::table('pats_ordenes_pago')->where('id_orden', $idOrden)->first();
            if ($orden && $orden->estatus_pago === 'CONFIRMADO') {
                return route('pagos') . '?pago=ok';
            }
        }

        // ── Buscar pasaporte del usuario ──────────────────────────────────────
        $pasaporte = DB::table('pats_pasaportes')
            ->where('correo', $correo)
            ->where('activo', 1)
            ->orderBy('fecha_alta', 'desc')
            ->first();

        DB::transaction(function () use (
            $p, $ahora, $paymentId, $registrationId, $cardBrand, $cardLast4,
            $frecuencia, $meses, $monto, $montoMembresia, $operacion, $referencia,
            $idOrden, $correo, $pasaporte, $checkout
        ) {
            $vigencia = $frecuencia === 'ANUAL'
                ? $ahora->copy()->addYear()->toDateString()
                : $ahora->copy()->addMonths($meses)->toDateString();
            $vencReal = $frecuencia === 'ANUAL'
                ? $ahora->copy()->addYear()->endOfDay()
                : $ahora->copy()->addMonths($meses)->endOfDay();

            // ── Actualizar / crear pasaporte ──────────────────────────────────
            if ($pasaporte) {
                DB::table('pats_pasaportes')
                    ->where('id_pasaporte', $pasaporte->id_pasaporte)
                    ->update([
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
                    'curp'                   => strtoupper($p['curp']         ?? ''),
                    'nombres'                => $p['nombres']                 ?? '',
                    'apellido_pa'            => $p['apellido_pa']             ?? '',
                    'apellido_ma'            => $p['apellido_ma']             ?? null,
                    'fecha_nacimiento'       => $p['fecha_nacimiento']        ?? null,
                    'correo'                 => $correo,
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

            // ── Actualizar orden ──────────────────────────────────────────────
            if ($idOrden) {
                DB::table('pats_ordenes_pago')
                    ->where('id_orden', $idOrden)
                    ->update([
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
                        'user_confirmo'                   => $correo,
                        'updated_at'                      => $ahora,
                    ]);
            }

            // ── Registro de pago ──────────────────────────────────────────────
            DB::table('pats_pagos_pasaporte')->insert([
                'id_orden'               => $idOrden,
                'id_pasaporte'           => $idPasaporte,
                'id_franquicia'          => $pasaporte->id_franquicia   ?? 1,
                'id_distribuidor'        => $pasaporte->id_distribuidor ?? 1,
                'id_tipo_precio'         => $p['id_tipo_precio']        ?? 2,
                'correo'                 => $correo,
                'curp'                   => strtoupper($p['curp']       ?? ''),
                'nombre_usuario'         => $p['nombres']               ?? '',
                'apellido_pa'            => $p['apellido_pa']           ?? '',
                'apellido_ma'            => $p['apellido_ma']           ?? null,
                'tipo_operacion'         => $operacion,
                'monto'                  => $monto,
                'monto_nominal_base'     => $montoMembresia,
                'monto_extra_recargo'    => (float) ($p['recargo']      ?? 0),
                'frecuencia'             => strtolower($frecuencia),
                'metodo_pago'            => 'tarjeta_recurrente_' . $cardBrand,
                'referencia_pago'        => $referencia,
                'referencia_externa'     => $paymentId,
                'transaccion_id_externa' => (string) $paymentId,
                'proveedor_pasarela'     => 'PROSA',
                'estatus_pago'           => 'confirmado',
                'fecha_pago'             => $ahora,
                'fecha_confirmacion'     => $ahora,
                'moneda'                 => 'MXN',
                'observaciones'          => "Prosa Recurrente:{$paymentId} {$cardBrand}···{$cardLast4}",
                'created_at'             => $ahora,
                'updated_at'             => $ahora,
            ]);

            // ── ProsaTransaction ──────────────────────────────────────────────
            ProsaTransaction::updateOrCreate(
                ['payment_id' => $paymentId],
                [
                    'user_id'         => $checkout->user_id,
                    'registration_id' => $registrationId,
                    'payment_type'    => 'DB',
                    'amount'          => $monto,
                    'currency'        => config('prosa.currency'),
                    'result_code'     => $paymentResult['resultCode']        ?? null,
                    'brand'           => $cardBrand,
                    'last4'           => $cardLast4,
                    'status'          => 'approved',
                    'origen'          => 'recurring_initial',
                ],
            );

            // ── Guardar suscripción ───────────────────────────────────────────
            if ($registrationId) {
                $nextCharge = $frecuencia === 'ANUAL'
                    ? $ahora->copy()->addYear()
                    : $ahora->copy()->addMonths($meses);

                ProsaSubscription::updateOrCreate(
                    ['registration_id' => $registrationId],
                    [
                        'user_id'         => $checkout->user_id,
                        'correo_usuario'  => $correo,
                        'card_brand'      => $cardBrand,
                        'card_last4'      => $cardLast4,
                        'frecuencia'      => $frecuencia,
                        'meses'           => $meses,
                        'id_tipo_precio'  => $p['id_tipo_precio'] ?? 2,
                        'amount'          => $monto,
                        'currency'        => 'MXN',
                        'status'          => 'active',
                        'failure_count'   => 0,
                        'last_charged_at' => $ahora,
                        'next_charge_at'  => $nextCharge,
                    ],
                );
            } else {
                Log::warning('PagosRecurringCheckout: pago aprobado pero OPPWA no devolvió registrationId.', [
                    'payment_id' => $paymentId,
                    'correo'     => $correo,
                ]);
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
