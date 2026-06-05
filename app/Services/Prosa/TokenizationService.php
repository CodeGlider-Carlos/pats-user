<?php

namespace App\Services\Prosa;

use App\DTO\Prosa\CardData;
use App\Exceptions\Prosa\ProsaException;

/**
 * Tokenización (cards on file) con OPPWA.
 *
 * Registro:  POST   /v1/registrations
 * Cobro:     POST   /v1/registrations/{id}/payments
 * Borrado:   DELETE /v1/registrations/{id}
 */
class TokenizationService
{
    public function __construct(
        private readonly ProsaHttpClient $http,
    ) {}

    /**
     * Registra una tarjeta y devuelve su `registrationId` + metadatos.
     *
     * @return array<string, mixed>
     *
     * @throws ProsaException
     */
    public function register(CardData $card): array
    {
        $params = $card->toParams();

        // Algunos canales OPPWA requieren el sender/channel entity ID al registrar.
        $senderId = (string) config('prosa.sender_id', '');
        if ($senderId !== '') {
            $params['standingInstruction.source'] = 'CIT';
        }

        $response = $this->http->post(
            config('prosa.endpoints.registrations'),
            $params,
        );

        $normalized = PaymentService::normalize($response);

        if (! $normalized['approved']) {
            throw new ProsaException(
                message: $normalized['resultDescription'] ?: 'No se pudo registrar la tarjeta.',
                resultCode: $normalized['resultCode'],
                raw: $response,
            );
        }

        return [
            'registrationId' => $response['id'] ?? null,
            'brand'          => $normalized['brand'] ?: $card->brand(),
            'last4'          => $normalized['last4'] ?? $card->last4(),
            'bin'            => $normalized['bin'] ?? $card->bin(),
            'holder'         => $normalized['holder'] ?? $card->holder,
            'raw'            => $response,
        ];
    }

    /**
     * Cobra contra una tarjeta previamente registrada.
     *
     * @return array<string, mixed>
     *
     * @throws ProsaException
     */
    public function chargeToken(
        string $registrationId,
        float $amount,
        ?string $cvv = null,
        string $currency = 'MXN',
        ?string $merchantTransactionId = null,
    ): array {
        $params = [
            'amount'      => number_format($amount, 2, '.', ''),
            'currency'    => $currency,
            'paymentType' => 'DB',
            'standingInstruction.source' => 'CIT',
            'standingInstruction.mode'   => 'REPEATED',
        ];

        if ($cvv !== null && $cvv !== '') {
            $params['card.cvv'] = preg_replace('/\D/', '', $cvv);
        }

        if ($merchantTransactionId !== null) {
            $params['merchantTransactionId'] = $merchantTransactionId;
        }

        $response = $this->http->post(
            sprintf(config('prosa.endpoints.reg_payments'), $registrationId),
            $params,
        );

        $normalized = PaymentService::normalize($response);

        if (! $normalized['approved']) {
            throw new ProsaException(
                message: $normalized['resultDescription'] ?: 'El pago fue rechazado.',
                resultCode: $normalized['resultCode'],
                raw: $response,
            );
        }

        return $normalized;
    }

    /**
     * Elimina una tarjeta registrada en OPPWA.
     *
     * @return array<string, mixed>
     */
    public function delete(string $registrationId): array
    {
        return $this->http->delete(
            sprintf(config('prosa.endpoints.registration'), $registrationId),
        );
    }
}
