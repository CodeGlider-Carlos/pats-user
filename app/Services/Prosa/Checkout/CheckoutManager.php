<?php

namespace App\Services\Prosa\Checkout;

use App\Models\ProsaPendingCheckout;
use RuntimeException;

/**
 * Registra los {@see CheckoutCompleter} y finaliza un checkout (frictionless
 * o tras el reto 3DS) despachando al completer correcto según el flujo.
 */
class CheckoutManager
{
    /** @var array<string, CheckoutCompleter> */
    private array $completers = [];

    /**
     * @param  iterable<CheckoutCompleter>  $completers
     */
    public function __construct(iterable $completers = [])
    {
        foreach ($completers as $completer) {
            $this->completers[$completer->flow()] = $completer;
        }
    }

    public function for(string $flow): CheckoutCompleter
    {
        if (! isset($this->completers[$flow])) {
            throw new RuntimeException("No hay completer registrado para el flujo '{$flow}'.");
        }

        return $this->completers[$flow];
    }

    /**
     * Finaliza el checkout según el resultado del pago. Devuelve la URL de
     * redirección (éxito o fallo).
     *
     * @param  array<string, mixed>  $paymentResult
     */
    public function finish(ProsaPendingCheckout $checkout, array $paymentResult): string
    {
        $completer = $this->for($checkout->flow);
        $approved = ($paymentResult['status'] ?? null) === 'approved'
            || ! empty($paymentResult['approved']);

        $checkout->update([
            'payment_id'         => $paymentResult['paymentId'] ?? $checkout->payment_id,
            'result_code'        => $paymentResult['resultCode'] ?? null,
            'result_description' => $paymentResult['resultDescription'] ?? null,
        ]);

        if (! $approved) {
            $checkout->update(['status' => ProsaPendingCheckout::STATUS_DECLINED]);

            return $completer->failUrl($checkout, $paymentResult['resultDescription'] ?? 'declined');
        }

        // Idempotencia: si ya se completó, no repetir el pipeline.
        if ($checkout->status === ProsaPendingCheckout::STATUS_COMPLETED) {
            return $completer->complete($checkout, $paymentResult);
        }

        $url = $completer->complete($checkout, $paymentResult);
        $checkout->update(['status' => ProsaPendingCheckout::STATUS_COMPLETED]);

        return $url;
    }
}
