<?php

namespace Tests\Unit;

use App\DTO\Prosa\MerchantData;
use PHPUnit\Framework\TestCase;

class ProsaMerchantDataTest extends TestCase
{
    public function test_valid_values_map_to_oppwa_merchant_params(): void
    {
        $params = (new MerchantData(
            url: 'https://patspassport.com',
            shippingCity: 'CDMX',
            shippingState: 'CMX',
            shippingCountry: 'MEX',
            shippingPostcode: '06000',
        ))->toParams();

        $this->assertSame([
            'merchant.url' => 'https://patspassport.com',
            'merchant.shipping.city' => 'CDMX',
            'merchant.shipping.state' => 'CMX',
            'merchant.shipping.country' => 'MEX',
            'merchant.shipping.postcode' => '06000',
        ], $params);
    }

    public function test_values_are_normalized_to_their_required_format(): void
    {
        $params = (new MerchantData(
            url: '  https://patspassport.com/checkout  ',
            shippingCity: 'Ciudad de México 11',     // sólo letras, máx 13
            shippingState: 'jal',                    // ISO 3166-2:MX (3 letras) en mayúsculas
            shippingCountry: 'mex',                  // alpha-3 en mayúsculas
            shippingPostcode: '64000-1234',          // alfanumérico, máx 10
        ))->toParams();

        $this->assertSame('https://patspassport.com/checkout', $params['merchant.url']);
        $this->assertSame('CiudaddeMxico', $params['merchant.shipping.city']); // sólo letras, recortado a 13
        $this->assertSame('JAL', $params['merchant.shipping.state']);
        $this->assertSame('MEX', $params['merchant.shipping.country']);
        $this->assertSame('640001234', $params['merchant.shipping.postcode']);
    }

    public function test_invalid_or_empty_values_are_omitted(): void
    {
        $params = (new MerchantData(
            url: '',
            shippingCity: '12345',          // sin letras → vacío
            shippingState: 'MEXICO',        // no son 3 letras → se omite
            shippingCountry: null,
            shippingPostcode: '   ',
        ))->toParams();

        $this->assertSame([], $params);
    }

    /**
     * Akamai (WAF de OPPWA) devuelve 403 si merchant.url apunta a un host no
     * público, lo que tumbaría toda la transacción. Por eso esas URLs se omiten.
     *
     * @dataProvider nonPublicUrls
     */
    public function test_non_public_merchant_urls_are_omitted(?string $url): void
    {
        $params = (new MerchantData(url: $url))->toParams();

        $this->assertArrayNotHasKey('merchant.url', $params);
    }

    /**
     * @return array<string, array{0: ?string}>
     */
    public static function nonPublicUrls(): array
    {
        return [
            'http localhost' => ['http://localhost'],
            'https localhost' => ['https://localhost'],
            'loopback ip' => ['http://127.0.0.1'],
            'host sin dominio' => ['https://intranet'],
            'sin esquema' => ['patspassport.com'],
            'esquema no http' => ['ftp://files.patspassport.com'],
            'nulo' => [null],
        ];
    }

    public function test_public_https_url_is_kept(): void
    {
        $params = (new MerchantData(url: 'https://shop.patspassport.com'))->toParams();

        $this->assertSame('https://shop.patspassport.com', $params['merchant.url']);
    }
}
