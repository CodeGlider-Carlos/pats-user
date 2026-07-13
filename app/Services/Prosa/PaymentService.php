<?php

namespace App\Services\Prosa;

use App\DTO\Prosa\ChargeData;
use App\DTO\Prosa\ThreeDSData;
use App\Exceptions\Prosa\ProsaException;

/**
 * Ventas directas (server-to-server) con OPPWA.
 *
 * Endpoint: POST /v1/payments  (paymentType DB)
 * Estado:   GET  /v1/payments/{id}
 */
class PaymentService
{
    public function __construct(
        private readonly ProsaHttpClient $http,
    ) {}

    /**
     * Ejecuta una venta con tarjeta. Si createRegistration está activo en el
     * DTO, la respuesta incluirá `registrationId` para guardar la tarjeta.
     *
     * @return array<string, mixed>
     *
     * @throws ProsaException             Si el pago es rechazado.
     * @throws \App\Exceptions\Prosa\ProsaTimeoutException
     */
    public function charge(ChargeData $data): array
    {
        $response = $this->http->post(
            config('prosa.endpoints.payments'),
            $data->toParams(),
        );

        return $this->interpret($response);
    }

    /**
     * Inicia un cobro con autenticación 3-D Secure 2. No lanza excepción:
     * devuelve un resultado con `status` = approved | challenge | declined.
     *
     * - approved: autenticación frictionless / capturado. Incluye paymentId.
     * - challenge: el emisor pide reto; incluye `redirect` (url/method/parameters)
     *   al que se debe enviar el navegador del cliente.
     * - declined: rechazado; incluye resultDescription.
     *
     * @return array<string, mixed>
     *
     * @throws \App\Exceptions\Prosa\ProsaTimeoutException
     */
    public function initiate(ChargeData $data, ThreeDSData $threeDs): array
    {
        $response = $this->http->post(
            config('prosa.endpoints.payments'),
            array_merge($data->toParams(), $threeDs->toParams()),
        );

        return self::classify($response);
    }

    /**
     * Consulta el estado de un pago previo. No lanza excepción por rechazo:
     * devuelve la interpretación para que el llamador concilie.
     *
     * @return array<string, mixed>
     */
    public function status(string $paymentId): array
    {
        $response = $this->http->get(
            sprintf(config('prosa.endpoints.payment'), $paymentId),
        );

        return self::normalize($response);
    }

    /**
     * Consulta el resultado final usando el `resourcePath` que OPPWA entrega al
     * regresar del reto 3DS (ej. "/v1/payments/{id}").
     *
     * @return array<string, mixed>
     */
    public function statusByResourcePath(string $resourcePath): array
    {
        $response = $this->http->get($resourcePath);

        return self::normalize($response);
    }

    /**
     * Clasifica una respuesta como approved | challenge | declined.
     *
     * @param  array<string, mixed>  $response
     * @return array<string, mixed>
     */
    public static function classify(array $response): array
    {
        $normalized = self::normalize($response);
        $code = $normalized['resultCode'];

        if ($normalized['approved']) {
            $normalized['status'] = 'approved';
        } elseif (! empty($normalized['redirect']) && ProsaResultCode::isChallengePending($code)) {
            $normalized['status'] = 'challenge';
        } else {
            $normalized['status'] = 'declined';
        }

        return $normalized;
    }

    /**
     * @param  array<string, mixed>  $response
     * @return array<string, mixed>
     *
     * @throws ProsaException
     */
    private function interpret(array $response): array
    {
        $normalized = self::normalize($response);

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
     * Normaliza la respuesta de OPPWA a una forma estable para la app.
     *
     * @param  array<string, mixed>  $response
     * @return array{paymentId: ?string, resultCode: string, resultDescription: string, approved: bool, pending: bool, registrationId: ?string, brand: string, last4: ?string, bin: ?string, holder: ?string, amount: ?string, currency: ?string, raw: array}
     */
    public static function normalize(array $response): array
    {
        $code = (string) ($response['result']['code'] ?? '');

        return [
            'paymentId'         => $response['id'] ?? null,
            'resultCode'        => $code,
            'resultDescription' => (string) ($response['result']['description'] ?? ''),
            'approved'          => $code !== '' && ProsaResultCode::isApproved($code),
            'pending'           => $code !== '' && ProsaResultCode::isPending($code),
            'registrationId'    => $response['registrationId'] ?? null,
            'brand'             => (string) ($response['paymentBrand'] ?? ''),
            'last4'             => $response['card']['last4Digits'] ?? null,
            'bin'               => $response['card']['bin'] ?? null,
            'holder'            => $response['card']['holder'] ?? null,
            'amount'            => $response['amount'] ?? null,
            'currency'          => $response['currency'] ?? null,
            'redirect'          => $response['redirect'] ?? null,
            'raw'               => $response,
        ];
    }
}
