<?php

namespace App\DTO\Prosa;

/**
 * Datos estáticos del comercio (parámetros `merchant.*`) exigidos por Prosa
 * en el "Checkout". A diferencia de {@see CardData} y {@see ThreeDSData}, no
 * describen al tarjetahabiente sino al comercio, y se envían en cada
 * transacción de pago.
 *
 * Cada valor se normaliza al formato que valida Prosa:
 *   url       AN255  cualquier carácter, máx. 255.
 *   city      A13    sólo letras, máx. 13.
 *   state     A3     ISO 3166-2:MX sin prefijo (3 letras).
 *   country   A3     ISO 3166-1 alpha-3 (3 letras).
 *   postcode  AN10   alfanumérico, máx. 10.
 */
class MerchantData
{
    public function __construct(
        public readonly ?string $url = null,
        public readonly ?string $shippingCity = null,
        public readonly ?string $shippingState = null,
        public readonly ?string $shippingCountry = null,
        public readonly ?string $shippingPostcode = null,
    ) {}

    /**
     * Construye el DTO desde la configuración del comercio. La URL cae a la
     * URL de la aplicación cuando no se configura una específica.
     */
    public static function fromConfig(): self
    {
        return new self(
            url: config('prosa.merchant.url') ?: config('app.url'),
            shippingCity: config('prosa.merchant.shipping.city'),
            shippingState: config('prosa.merchant.shipping.state'),
            shippingCountry: config('prosa.merchant.shipping.country'),
            shippingPostcode: config('prosa.merchant.shipping.postcode'),
        );
    }

    /**
     * Devuelve sólo los parámetros con un valor válido tras la normalización.
     *
     * @return array<string, string>
     */
    public function toParams(): array
    {
        $params = [];

        if ($url = $this->publicUrl((string) $this->url)) {
            $params['merchant.url'] = $url;
        }
        if ($city = $this->lettersOnly((string) $this->shippingCity, 13)) {
            $params['merchant.shipping.city'] = $city;
        }
        if ($state = $this->isoCode((string) $this->shippingState)) {
            $params['merchant.shipping.state'] = $state;
        }
        if ($country = $this->isoCode((string) $this->shippingCountry)) {
            $params['merchant.shipping.country'] = $country;
        }
        if ($postcode = $this->alphanumeric((string) $this->shippingPostcode, 10)) {
            $params['merchant.shipping.postcode'] = $postcode;
        }

        return $params;
    }

    /**
     * Devuelve la URL sólo si es absoluta, con esquema http(s) y host público.
     * El WAF (Akamai) frente a OPPWA rechaza con 403 valores no públicos como
     * "http://localhost", por lo que se omiten en vez de enviarse y tumbar la
     * transacción completa.
     */
    private function publicUrl(string $value): string
    {
        $value = substr(trim($value), 0, 255);

        if ($value === '') {
            return '';
        }

        $scheme = strtolower((string) parse_url($value, PHP_URL_SCHEME));
        $host = strtolower((string) parse_url($value, PHP_URL_HOST));

        if (! in_array($scheme, ['http', 'https'], true) || $host === '') {
            return '';
        }

        // Sólo hosts públicos: descartar loopback/local y hosts sin dominio.
        if (in_array($host, ['localhost', '127.0.0.1', '::1'], true) || ! str_contains($host, '.')) {
            return '';
        }

        return $value;
    }

    private function lettersOnly(string $value, int $max): string
    {
        return substr((string) preg_replace('/[^A-Za-z]/', '', $value), 0, $max);
    }

    private function alphanumeric(string $value, int $max): string
    {
        return substr((string) preg_replace('/[^A-Za-z0-9]/', '', $value), 0, $max);
    }

    /**
     * Código ISO de 3 letras (state/country). Devuelve cadena vacía cuando el
     * valor no queda en exactamente 3 letras, para no enviar un parámetro que
     * Prosa rechazaría.
     */
    private function isoCode(string $value): string
    {
        $code = strtoupper((string) preg_replace('/[^A-Za-z]/', '', $value));

        return strlen($code) === 3 ? $code : '';
    }
}
