<?php

namespace App\DTO\Prosa;

use Illuminate\Http\Request;

/**
 * Datos para la autenticación 3-D Secure 2 (server-to-server con OPPWA).
 *
 * Incluye: shopperResultUrl (a donde el ACS regresa al cliente), datos del
 * cliente (email/ip/userAgent), dirección de facturación y los datos del
 * navegador requeridos por el flujo del reto.
 *
 * @param array{street1?: string, city?: string, state?: string, country?: string, postcode?: string}|null $billing
 * @param array{language?: string, screenHeight?: string|int, screenWidth?: string|int, timezone?: string|int, colorDepth?: string|int, javaEnabled?: bool}|null $browser
 */
class ThreeDSData
{
    public function __construct(
        public readonly string $shopperResultUrl,
        public readonly ?string $email = null,
        public readonly ?string $ip = null,
        public readonly ?string $userAgent = null,
        public readonly ?string $acceptHeader = null,
        public readonly ?string $givenName = null,
        public readonly ?string $surname = null,
        public readonly ?array $billing = null,
        public readonly ?array $browser = null,
        public readonly string $challengeWindow = '05',
    ) {}

    /**
     * Construye el DTO tomando ip/userAgent/acceptHeader de la petición actual
     * y el resto de los datos provistos por el llamador.
     *
     * @param array{street1?: string, city?: string, state?: string, country?: string, postcode?: string}|null $billing
     * @param array<string, mixed>|null $browser
     */
    public static function fromRequest(
        Request $request,
        string $shopperResultUrl,
        ?string $email = null,
        ?string $givenName = null,
        ?string $surname = null,
        ?array $billing = null,
        ?array $browser = null,
    ): self {
        return new self(
            shopperResultUrl: $shopperResultUrl,
            email:            $email,
            ip:               $request->ip(),
            userAgent:        substr((string) $request->userAgent(), 0, 2048),
            acceptHeader:     substr((string) $request->header('Accept', 'text/html'), 0, 2048),
            givenName:        $givenName,
            surname:          $surname,
            billing:          $billing,
            browser:          $browser,
            challengeWindow:  (string) config('prosa.three_ds.challenge_window', '05'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toParams(): array
    {
        $params = [
            'shopperResultUrl' => $this->shopperResultUrl,
        ];

        if ($this->email) {
            $params['customer.email'] = $this->email;
        }
        if ($this->ip) {
            $params['customer.ip'] = $this->ip;
        }
        if ($this->givenName) {
            $params['customer.givenName'] = $this->givenName;
        }
        if ($this->surname) {
            $params['customer.surname'] = $this->surname;
        }

        if ($this->billing) {
            $map = [
                'street1'  => 'billing.street1',
                'city'     => 'billing.city',
                'state'    => 'billing.state',
                'country'  => 'billing.country',
                'postcode' => 'billing.postcode',
            ];
            foreach ($map as $key => $param) {
                if (! empty($this->billing[$key])) {
                    $params[$param] = $this->billing[$key];
                }
            }
            if (empty($params['billing.country'])) {
                $params['billing.country'] = (string) config('prosa.three_ds.country', 'MX');
            }
        }

        // ── Datos del navegador (3DS2) ──
        $b = $this->browser ?? [];
        $params['customer.browser.acceptHeader']     = $this->acceptHeader ?: 'text/html';
        $params['customer.browser.userAgent']        = $this->userAgent ?: '';
        $params['customer.browser.language']         = $b['language'] ?? 'es-MX';
        $params['customer.browser.screenHeight']     = (string) ($b['screenHeight'] ?? '900');
        $params['customer.browser.screenWidth']      = (string) ($b['screenWidth'] ?? '1440');
        $params['customer.browser.timezone']         = (string) ($b['timezone'] ?? '360');
        $params['customer.browser.screenColorDepth'] = (string) ($b['colorDepth'] ?? '24');
        $params['customer.browser.javaEnabled']      = ! empty($b['javaEnabled']) ? 'true' : 'false';
        $params['customer.browser.javascriptEnabled'] = 'true';
        $params['customer.browser.challengeWindow']  = $this->challengeWindow;

        return $params;
    }
}
