<?php

namespace Tests\Feature;

use App\DTO\Prosa\CardData;
use App\DTO\Prosa\ChargeData;
use App\DTO\Prosa\ThreeDSData;
use App\Exceptions\Prosa\ProsaException;
use App\Services\Prosa\PaymentService;
use App\Services\Prosa\ProsaHttpClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProsaPaymentServiceTest extends TestCase
{
    private function paymentService(): PaymentService
    {
        return new PaymentService(new ProsaHttpClient(
            baseUrl: 'https://eu-test.oppwa.com',
            accessToken: 'test-token',
            entityId: 'test-entity',
            timeout: 5,
            connectTimeout: 5,
            retryTimes: 0,
        ));
    }

    private function card(): CardData
    {
        return CardData::fromForm(
            number: '4200000000000000',
            holder: 'JUAN PEREZ',
            expMonth: '12',
            expYear: '30',
            cvv: '123',
        );
    }

    /**
     * Parsea el cuerpo form-urlencoded preservando los puntos de las claves
     * OPPWA (card.number, merchant.url, billing.street1, …). parse_str() de
     * PHP los convierte a guiones bajos, por lo que no sirve aquí.
     *
     * @return array<string, string>
     */
    private function bodyParams(\Illuminate\Http\Client\Request $request): array
    {
        $params = [];

        foreach (explode('&', $request->body()) as $pair) {
            if ($pair === '') {
                continue;
            }
            [$key, $value] = array_pad(explode('=', $pair, 2), 2, '');
            $params[urldecode($key)] = urldecode($value);
        }

        return $params;
    }

    public function test_charge_sends_expected_oppwa_params_and_returns_payment(): void
    {
        Http::fake([
            'eu-test.oppwa.com/v1/payments' => Http::response([
                'id' => '8ac7a4a1payment',
                'paymentBrand' => 'VISA',
                'amount' => '800.00',
                'currency' => 'MXN',
                'result' => ['code' => '000.100.110', 'description' => 'Request successfully processed'],
                'card' => ['bin' => '420000', 'last4Digits' => '0000'],
            ], 200),
        ]);

        $result = $this->paymentService()->charge(new ChargeData(
            card: $this->card(),
            amount: 800.00,
            currency: 'MXN',
            merchantTransactionId: 'PATS-TEST',
        ));

        $this->assertTrue($result['approved']);
        $this->assertSame('8ac7a4a1payment', $result['paymentId']);
        $this->assertSame('VISA', $result['brand']);
        $this->assertSame('0000', $result['last4']);

        Http::assertSent(function ($request) {
            $body = $this->bodyParams($request);

            return $request->url() === 'https://eu-test.oppwa.com/v1/payments'
                && $request->hasHeader('Authorization', 'Bearer test-token')
                && $body['entityId'] === 'test-entity'
                && $body['amount'] === '800.00'
                && $body['currency'] === 'MXN'
                && $body['paymentType'] === 'DB'
                && $body['paymentBrand'] === 'VISA'
                && $body['card.number'] === '4200000000000000'
                && $body['card.expiryMonth'] === '12'
                && $body['card.expiryYear'] === '2030'
                && $body['card.cvv'] === '123';
        });
    }

    public function test_charge_injects_testmode_descriptor_and_alphanumeric_mtx(): void
    {
        Http::fake([
            'eu-test.oppwa.com/v1/payments' => Http::response([
                'id' => '8ac7a4a1payment',
                'result' => ['code' => '000.100.110', 'description' => 'ok'],
            ], 200),
        ]);

        $service = new PaymentService(new ProsaHttpClient(
            baseUrl: 'https://eu-test.oppwa.com',
            accessToken: 'test-token',
            entityId: 'test-entity',
            descriptor: '7639599',
            testMode: 'EXTERNAL',
            timeout: 5,
            connectTimeout: 5,
            retryTimes: 0,
        ));

        $service->charge(new ChargeData(
            card: $this->card(),
            amount: 800.00,
            merchantTransactionId: 'PATS-2026/06#02', // contiene caracteres no alfanuméricos
        ));

        Http::assertSent(function ($request) {
            $body = $this->bodyParams($request);

            return $body['testMode'] === 'EXTERNAL'
                && $body['descriptor'] === '7639599'
                && ctype_alnum($body['merchantTransactionId'])
                && strlen($body['merchantTransactionId']) >= 8;
        });
    }

    public function test_charge_injects_merchant_params_in_the_checkout(): void
    {
        Http::fake([
            'eu-test.oppwa.com/v1/payments' => Http::response([
                'id' => '8ac7a4a1payment',
                'result' => ['code' => '000.100.110', 'description' => 'ok'],
            ], 200),
        ]);

        $service = new PaymentService(new ProsaHttpClient(
            baseUrl: 'https://eu-test.oppwa.com',
            accessToken: 'test-token',
            entityId: 'test-entity',
            timeout: 5,
            connectTimeout: 5,
            retryTimes: 0,
            merchantParams: [
                'merchant.url' => 'https://patspassport.com',
                'merchant.shipping.city' => 'CDMX',
                'merchant.shipping.state' => 'CMX',
                'merchant.shipping.country' => 'MEX',
                'merchant.shipping.postcode' => '06000',
            ],
        ));

        $service->charge(new ChargeData(
            card: $this->card(),
            amount: 800.00,
            merchantTransactionId: 'PATS-TEST',
        ));

        Http::assertSent(function ($request) {
            $body = $this->bodyParams($request);

            return $body['merchant.url'] === 'https://patspassport.com'
                && $body['merchant.shipping.city'] === 'CDMX'
                && $body['merchant.shipping.state'] === 'CMX'
                && $body['merchant.shipping.country'] === 'MEX'
                && $body['merchant.shipping.postcode'] === '06000';
        });
    }

    public function test_initiate_maps_billing_to_oppwa_params(): void
    {
        Http::fake([
            'eu-test.oppwa.com/v1/payments' => Http::response([
                'id' => '8ac7a4a1payment',
                'result' => ['code' => '000.100.110', 'description' => 'ok'],
            ], 200),
        ]);

        $threeDs = new ThreeDSData(
            shopperResultUrl: 'https://patspassport.com/prosa/3ds/return/PATSTEST',
            billing: [
                'street1' => 'Av Reforma 100',
                'city' => 'Monterrey',
                'postcode' => '64000',
            ],
        );

        $this->paymentService()->initiate(
            new ChargeData(card: $this->card(), amount: 800.00, merchantTransactionId: 'PATS-TEST'),
            $threeDs,
        );

        Http::assertSent(function ($request) {
            $body = $this->bodyParams($request);

            return $body['billing.street1'] === 'Av Reforma 100'
                && $body['billing.city'] === 'Monterrey'
                && $body['billing.postcode'] === '64000'
                && $body['billing.country'] === 'MX'; // default de config('prosa.three_ds.country')
        });
    }

    public function test_charge_throws_on_declined_result_code(): void
    {
        Http::fake([
            'eu-test.oppwa.com/v1/payments' => Http::response([
                'id' => '8ac7a4a1declined',
                'result' => ['code' => '800.100.151', 'description' => 'transaction declined (invalid card)'],
            ], 200),
        ]);

        $this->expectException(ProsaException::class);

        $this->paymentService()->charge(new ChargeData(
            card: $this->card(),
            amount: 800.00,
        ));
    }
}
