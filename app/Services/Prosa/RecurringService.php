<?php

namespace App\Services\Prosa;

use App\DTO\Prosa\CardData;
use App\Exceptions\Prosa\ProsaException;
use App\Services\Prosa\PaymentService;

/**
 * Cobros recurrentes (stored credentials / standing instructions) con OPPWA.
 *
 * Primer cobro (CIT, tarjetahabiente presente): registra la credencial y
 * marca standingInstruction.mode=INITIAL. Devuelve el `registrationId` para
 * los cobros subsecuentes.
 *
 * Cobros subsecuentes (MIT, iniciados por el comercio): usan el
 * `registrationId` con standingInstruction.mode=REPEATED.
 */
class RecurringService
{
    public function __construct(
        private readonly ProsaHttpClient $http,
    ) {}

    /**
     * Cobro inicial que deja la tarjeta lista para recurrencia.
     *
     * @param  array<string, mixed>  $extraParams  Parámetros adicionales, p.ej. 3DS (shopperResultUrl, browser, billing).
     * @return array<string, mixed>
     *
     * @throws ProsaException
     */
    public function startRecurring(
        CardData $card,
        float $amount,
        string $currency = 'MXN',
        ?string $merchantTransactionId = null,
        array $extraParams = [],
    ): array {
        $params = array_merge([
            'amount'      => number_format($amount, 2, '.', ''),
            'currency'    => $currency,
            'paymentType' => 'DB',
            'createRegistration'         => 'true',
            'standingInstruction.mode'   => 'INITIAL',
            'standingInstruction.type'   => 'RECURRING',
            'standingInstruction.source' => 'CIT',
        ], $card->toParams(), $extraParams);

        if ($merchantTransactionId !== null) {
            $params['merchantTransactionId'] = $merchantTransactionId;
        }

        $response = $this->http->post(config('prosa.endpoints.payments'), $params);

        // Si se incluyeron parámetros 3DS puede haber un reto: usar classify
        // para devolver approved | challenge | declined en vez de solo approved/throw.
        if (! empty($extraParams['shopperResultUrl'])) {
            return PaymentService::classify($response);
        }

        return $this->approvedOrFail($response);
    }

    /**
     * Cobro recurrente subsecuente contra una credencial almacenada.
     *
     * @return array<string, mixed>
     *
     * @throws ProsaException
     */
    public function chargeRecurring(
        string $registrationId,
        float $amount,
        string $currency = 'MXN',
        ?string $merchantTransactionId = null,
    ): array {
        $params = [
            'amount'      => number_format($amount, 2, '.', ''),
            'currency'    => $currency,
            'paymentType' => 'DB',
            'standingInstruction.mode'   => 'REPEATED',
            'standingInstruction.type'   => 'RECURRING',
            'standingInstruction.source' => 'MIT',
        ];

        if ($merchantTransactionId !== null) {
            $params['merchantTransactionId'] = $merchantTransactionId;
        }

        $response = $this->http->post(
            sprintf(config('prosa.endpoints.reg_payments'), $registrationId),
            $params,
        );

        return $this->approvedOrFail($response);
    }

    /**
     * @param  array<string, mixed>  $response
     * @return array<string, mixed>
     *
     * @throws ProsaException
     */
    private function approvedOrFail(array $response): array
    {
        $normalized = PaymentService::normalize($response);

        if (! $normalized['approved']) {
            throw new ProsaException(
                message: $normalized['resultDescription'] ?: 'El cobro recurrente fue rechazado.',
                resultCode: $normalized['resultCode'],
                raw: $response,
            );
        }

        return $normalized;
    }
}
