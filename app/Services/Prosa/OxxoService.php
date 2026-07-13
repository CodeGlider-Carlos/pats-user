<?php

namespace App\Services\Prosa;

use App\Exceptions\Prosa\ProsaException;

/**
 * Genera fichas de pago OXXO usando OPPWA.
 *
 * Endpoint: POST /v1/payments  (paymentType=PA, paymentBrand=OXXO)
 *
 * El pago es asíncrono: OPPWA devuelve un `redirect` con el número de
 * referencia/código de barras; la confirmación llega vía webhook cuando
 * el cliente paga en tienda.
 */
class OxxoService
{
    public function __construct(
        private readonly ProsaHttpClient $http,
    ) {}

    /**
     * Genera una ficha de pago OXXO y devuelve los datos del voucher.
     *
     * @param  array{amount: float, email: string, givenName: string, surname: string, ip: string, merchantTransactionId?: string}  $data
     * @return array{paymentId: string, reference: ?string, redirectUrl: ?string, resultCode: string, raw: array}
     *
     * @throws ProsaException
     */
    public function createVoucher(array $data): array
    {
        $params = [
            'paymentType'      => 'PA',
            'paymentBrand'     => 'OXXO',
            'amount'           => number_format((float) $data['amount'], 2, '.', ''),
            'currency'         => 'MXN',
            'customer.email'   => $data['email'],
            'customer.givenName' => $data['givenName'],
            'customer.surname'   => $data['surname'],
            'customer.ip'        => $data['ip'],
            'billing.country'    => 'MX',
        ];

        if (! empty($data['merchantTransactionId'])) {
            $params['merchantTransactionId'] = $data['merchantTransactionId'];
        }

        $response   = $this->http->post(config('prosa.endpoints.payments'), $params);
        $normalized = PaymentService::normalize($response);

        // OPPWA devuelve 000.200.xxx para pagos pendientes (aceptados pero sin cobrar aún).
        if (! $normalized['approved'] && ! $normalized['pending']) {
            throw new ProsaException(
                message: $normalized['resultDescription'] ?: 'No se pudo generar la ficha OXXO.',
                resultCode: $normalized['resultCode'],
                raw: $response,
            );
        }

        // Extraer referencia / código de barras del objeto `redirect`.
        $reference   = null;
        $redirectUrl = null;

        if (! empty($response['redirect'])) {
            $redirectUrl = $response['redirect']['url'] ?? null;

            foreach ($response['redirect']['parameters'] ?? [] as $param) {
                $name = $param['name'] ?? '';
                if (in_array($name, ['barcode', 'reference', 'voucher'], true)) {
                    $reference = $param['value'];
                    break;
                }
            }

            // Fallback: si la referencia viene directamente en el body.
            if ($reference === null) {
                $reference = $response['descriptor'] ?? $response['merchantTransactionId'] ?? null;
            }
        }

        return [
            'paymentId'   => $normalized['paymentId'],
            'reference'   => $reference,
            'redirectUrl' => $redirectUrl,
            'resultCode'  => $normalized['resultCode'],
            'raw'         => $response,
        ];
    }
}
