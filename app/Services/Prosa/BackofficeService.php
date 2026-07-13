<?php

namespace App\Services\Prosa;

use App\Exceptions\Prosa\ProsaException;

/**
 * Operaciones de post-venta sobre un pago previo con OPPWA.
 *
 * Endpoint: POST /v1/payments/{paymentId}
 *  - RF (Refund):   devuelve un pago ya capturado (total o parcial).
 *  - RV (Reversal): cancela una preautorización o un cargo antes del corte.
 */
class BackofficeService
{
    public function __construct(
        private readonly ProsaHttpClient $http,
    ) {}

    /**
     * @return array<string, mixed>
     *
     * @throws ProsaException
     */
    public function refund(string $paymentId, float $amount, string $currency = 'MXN'): array
    {
        return $this->operate($paymentId, 'RF', [
            'amount'   => number_format($amount, 2, '.', ''),
            'currency' => $currency,
        ]);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ProsaException
     */
    public function reversal(string $paymentId): array
    {
        return $this->operate($paymentId, 'RV', []);
    }

    /**
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     *
     * @throws ProsaException
     */
    private function operate(string $paymentId, string $paymentType, array $extra): array
    {
        $response = $this->http->post(
            sprintf(config('prosa.endpoints.payment'), $paymentId),
            array_merge(['paymentType' => $paymentType], $extra),
        );

        $normalized = PaymentService::normalize($response);

        if (! $normalized['approved']) {
            throw new ProsaException(
                message: $normalized['resultDescription'] ?: 'La operación fue rechazada.',
                resultCode: $normalized['resultCode'],
                raw: $response,
            );
        }

        return $normalized;
    }
}
