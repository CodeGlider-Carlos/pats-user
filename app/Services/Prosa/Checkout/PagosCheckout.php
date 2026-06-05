<?php

namespace App\Services\Prosa\Checkout;

use App\Models\ProsaPendingCheckout;
use App\Models\ProsaToken;
use App\Models\ProsaTransaction;

/**
 * Completer del portal de pagos (venta directa de membresía). No corre un
 * pipeline de negocio: sólo registra la {@see ProsaTransaction} aprobada.
 */
class PagosCheckout implements CheckoutCompleter
{
    public const FLOW = 'pagos';

    public function flow(): string
    {
        return self::FLOW;
    }

    public function complete(ProsaPendingCheckout $checkout, array $paymentResult): string
    {
        $payload        = $checkout->payload ?? [];
        $registrationId = $paymentResult['registrationId'] ?? null;
        $brand          = $paymentResult['brand'] ?: ($payload['brand'] ?? null);
        $last4          = $paymentResult['last4'] ?? ($payload['last4'] ?? null);

        ProsaTransaction::updateOrCreate(
            ['payment_id' => $paymentResult['paymentId']],
            [
                'user_id'            => $checkout->user_id,
                'registration_id'    => $registrationId,
                'payment_type'       => 'DB',
                'amount'             => $checkout->amount,
                'currency'           => config('prosa.currency'),
                'result_code'        => $paymentResult['resultCode'] ?? null,
                'result_description' => $paymentResult['resultDescription'] ?? null,
                'brand'              => $brand,
                'last4'              => $last4,
                'status'             => 'approved',
                'origen'             => $payload['origen'] ?? 'one_step',
                'raw_response'       => $paymentResult['raw'] ?? null,
            ],
        );

        // ── Guardar tarjeta si el usuario lo solicitó ──────────────────────────
        // OPPWA retorna registrationId cuando el pago se procesó con
        // createRegistration=true. Sólo guardamos si hay user_id válido.
        if (
            ! empty($payload['saveCard'])
            && $registrationId
            && $checkout->user_id
        ) {
            $alreadySaved = ProsaToken::where('registration_id', $registrationId)->exists();

            if (! $alreadySaved) {
                $token = ProsaToken::create([
                    'user_id'         => $checkout->user_id,
                    'registration_id' => $registrationId,
                    'alias'           => $payload['alias'] ?? null,
                    'card_brand'      => $brand,
                    'card_last4'      => $last4,
                    'card_bin'        => $paymentResult['bin'] ?? null,
                    'cardholder_name' => $payload['holder'] ?? null,
                    'exp_month'       => $payload['expMonth'] ?? null,
                    'exp_year'        => $payload['expYear'] ?? null,
                    'status'          => 'active',
                    'is_default'      => false,
                ]);

                // Si es la primera tarjeta del usuario, marcarla como default.
                if (ProsaToken::active()->forUser($checkout->user_id)->count() === 1) {
                    $token->setAsDefault();
                }
            }
        }

        return route('pagos') . '?pago=ok';
    }

    public function failUrl(ProsaPendingCheckout $checkout, string $reason): string
    {
        return route('pagos') . '?pago=error';
    }
}
