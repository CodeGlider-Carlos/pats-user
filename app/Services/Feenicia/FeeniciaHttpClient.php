<?php

namespace App\Services\Feenicia;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Exceptions\Feenicia\FeeniciaException;
use App\Exceptions\Feenicia\FeeniciaTimeoutException;

/**
 * Cliente HTTP base para todos los requests a Feenicia.
 *
 * Responsabilidades:
 *  - Agregar headers obligatorios (Accept, Content-Type, x-requested-with)
 *  - Firmar automáticamente cada request con FeeniciaSignatureService
 *  - Loggear request y response para debugging
 *  - Lanzar excepciones tipadas según el responseCode
 *  - Detectar timeouts para que el llamador pueda enviar un Reversal
 */
class FeeniciaHttpClient
{
    public function __construct(
        private readonly string                   $baseUrl,
        private readonly FeeniciaSignatureService $signatureService,
        private readonly int                      $timeout        = 30,
        private readonly int                      $connectTimeout = 10,
    ) {}

    /**
     * Realiza un POST firmado a un endpoint de Feenicia.
     *
     * @param  string               $endpoint  Path relativo, ej: '/v1/atna/sale/oneStepSaleManual'
     * @param  array<string, mixed> $payload   Body del request (ya con campos encriptados)
     * @return array<string, mixed>            Respuesta decodificada como array
     *
     * @throws FeeniciaTimeoutException  Si hay timeout — el llamador DEBE enviar Reversal
     * @throws FeeniciaException         Si responseCode != '00'
     */
    public function post(string $endpoint, array $payload): array
    {
        $url = rtrim($this->baseUrl, '/') . $endpoint;

        // ── La firma se genera con el payload YA encriptado
        // que es lo que se enviará — Feenicia firma lo que recibe
        $signature = $this->signatureService->buildHeader($payload);

        $headers = [
            'Accept'           => 'application/json',
            'Content-Type'     => 'application/json',
            'x-requested-with' => $signature,
        ];

        Log::channel('feenicia')->info('Feenicia request', [
            'endpoint' => $endpoint,
            'payload'  => $this->maskSensitiveFields($payload),
        ]);

        try {
            $response = Http::withHeaders($headers)
                ->timeout($this->timeout)
                ->connectTimeout($this->connectTimeout)
                ->post($url, $payload);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::channel('feenicia')->error('Feenicia timeout', [
                'endpoint' => $endpoint,
                'message'  => $e->getMessage(),
            ]);

            throw new FeeniciaTimeoutException(
                endpoint: $endpoint,
                payload: $payload,
                previous: $e,
            );
        }

        return $this->handleResponse($response, $endpoint);
    }

    /**
     * GET firmado (usado en tokenización: /balder/auth/getKey, /balder/token/getCards).
     *
     * @param  string               $endpoint
     * @param  array<string, mixed> $query     Query params opcionales
     * @return array<string, mixed>
     */
    public function get(string $endpoint, array $query = []): array
    {
        $url       = rtrim($this->baseUrl, '/') . $endpoint;

        // Para GET, firmamos con el query params o un array vacío
        $signature = $this->signatureService->buildHeader($query ?: []);

        $headers = [
            'Accept'           => 'application/json',
            'Content-Type'     => 'application/json',
            'x-requested-with' => $signature,
        ];

        try {
            $response = Http::withHeaders($headers)
                ->timeout($this->timeout)
                ->connectTimeout($this->connectTimeout)
                ->get($url, $query);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw new FeeniciaTimeoutException(
                endpoint: $endpoint,
                payload: $query,
                previous: $e,
            );
        }

        return $this->handleResponse($response, $endpoint);
    }

    // ──────────────────────────────────────────────
    //  Internos
    // ──────────────────────────────────────────────

    /**
     * Procesa la respuesta HTTP: valida status, parsea JSON y evalúa responseCode.
     *
     * @throws FeeniciaException
     */
    private function handleResponse(Response $response, string $endpoint): array
    {
        Log::channel('feenicia')->info('Feenicia response', [
            'endpoint' => $endpoint,
            'status'   => $response->status(),
            'body'     => $response->body(),
        ]);

        if ($response->failed()) {
            throw new FeeniciaException(
                responseCode: (string) $response->status(),
                message: "Feenicia HTTP error {$response->status()} en {$endpoint}",
                body: $response->json() ?? [],
            );
        }

        $data = $response->json();

        if (!is_array($data)) {
            throw new FeeniciaException(
                responseCode: 'PARSE_ERROR',
                message: "Feenicia: respuesta no es JSON válido en {$endpoint}",
                body: [],
            );
        }

        // Feenicia usa 'responseCode' o 'ResponseCode' dependiendo del endpoint
        $code = $data['responseCode'] ?? $data['ResponseCode'] ?? null;

        if ($code !== null && $code !== '00') {
            throw new FeeniciaException(
                responseCode: (string) $code,
                message: "Feenicia rechazó la operación con código: {$code}",
                body: $data,
            );
        }

        return $data;
    }

    /**
     * Enmascara campos sensibles antes de loggear.
     *
     * @param  array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function maskSensitiveFields(array $payload): array
    {
        $sensitive = ['pan', 'cvv2', 'cardholderName', 'expDate', 'Email'];

        foreach ($sensitive as $field) {
            if (isset($payload[$field])) {
                $payload[$field] = '***REDACTED***';
            }
        }

        return $payload;
    }
}
