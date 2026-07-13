<?php

namespace App\Services\Prosa\Checkout;

use App\Models\ProsaPendingCheckout;
use Illuminate\Support\Facades\DB;

/**
 * Completer genérico para los flujos de solicitud (franquicia, distribuidor,
 * pats-registro, distribuidor-link).
 *
 * Al regresar del reto 3DS, el registro de solicitud ya existe: sólo se
 * registra el pago en `pats_pagos` y se actualiza el estatus del checkout.
 */
class SolicitudCheckout implements CheckoutCompleter
{
    public function __construct(
        private readonly string $flowName,
        private readonly string $tipoSolicitud,
        private readonly string $successRouteOrPath,
        private readonly string $failRouteOrPath,
    ) {}

    public function flow(): string
    {
        return $this->flowName;
    }

    public function complete(ProsaPendingCheckout $checkout, array $paymentResult): string
    {
        $payload    = $checkout->payload ?? [];
        $paymentId  = $paymentResult['paymentId'] ?? '';

        // ── Caso: solicitud ya creada (id_solicitud provisto) ──
        // Registrar el pago en pats_pagos si todavía no existe.
        if (! empty($payload['id_solicitud']) && $paymentId) {
            $alreadyRecorded = DB::table('pats_pagos')
                ->where('pasarela', 'prosa')
                ->where('referencia_pasarela', $paymentId)
                ->exists();

            if (! $alreadyRecorded) {
                DB::table('pats_pagos')->insert([
                    'tipo_solicitud'      => $this->tipoSolicitud,
                    'id_solicitud'        => $payload['id_solicitud'],
                    'pasarela'            => 'prosa',
                    'referencia_pasarela' => $paymentId,
                    'estatus'             => 'succeeded',
                    'monto'               => $checkout->amount,
                    'moneda'              => 'MXN',
                    'metadata_json'       => json_encode(['resultCode' => $paymentResult['resultCode'] ?? null]),
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);
            }

            return $payload['success_url'] ?? $this->successRouteOrPath;
        }

        // ── Caso: la solicitud AÚN NO fue creada (reto 3DS antes del submit) ──
        // Redirigir al formulario con el payment_id confirmado; el JS re-habilita
        // el botón "Finalizar solicitud" para que el usuario reenvíe el formulario.
        $formUrl  = $payload['form_url'] ?? $this->failRouteOrPath;
        $separator = str_contains($formUrl, '?') ? '&' : '?';

        return $formUrl . $separator . http_build_query([
            'prosa_payment_id' => $paymentId,
            '3ds'              => 'ok',
        ]);
    }

    public function failUrl(ProsaPendingCheckout $checkout, string $reason): string
    {
        $payload = $checkout->payload ?? [];

        return $payload['fail_url'] ?? $this->failRouteOrPath;
    }
}
