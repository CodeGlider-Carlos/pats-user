<?php

namespace App\Services\Prosa;

use App\Exceptions\Prosa\ProsaTimeoutException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Cliente HTTP de bajo nivel para la API OPPWA / ACI (Prosa).
 *
 * Responsabilidades:
 *  - Autenticación Bearer + inyección automática de `entityId`.
 *  - Envío form-urlencoded (POST), query (GET) y DELETE.
 *  - Conversión de timeouts de conexión en {@see ProsaTimeoutException}.
 *  - Decodificación de la respuesta JSON a array.
 *
 * La interpretación del `result.code` se delega a los servicios
 * ({@see PaymentService}, etc.) mediante {@see ProsaResultCode}.
 */
class ProsaHttpClient
{
    /**
     * @param  array<string, string>  $merchantParams  Datos del comercio (merchant.*) a inyectar en cada transacción.
     */
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $accessToken,
        private readonly string $entityId,
        private readonly ?string $descriptor = null,
        private readonly ?string $testMode = null,
        private readonly int $timeout = 30,
        private readonly int $connectTimeout = 10,
        private readonly int $retryTimes = 1,
        private readonly int $retrySleepMs = 500,
        private readonly array $merchantParams = [],
    ) {}

    public function entityId(): string
    {
        return $this->entityId;
    }

    /**
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     *
     * @throws ProsaTimeoutException
     */
    public function post(string $path, array $params): array
    {
        $params['entityId'] = $this->entityId;

        // Parámetros exigidos por el banco en cada transacción.
        if ($this->descriptor !== null && $this->descriptor !== '' && ! isset($params['descriptor'])) {
            $params['descriptor'] = $this->descriptor;
        }

        if ($this->testMode !== null && $this->testMode !== '' && ! isset($params['testMode'])) {
            $params['testMode'] = $this->testMode;
        }

        // Datos del comercio (merchant.url, merchant.shipping.*) exigidos por
        // Prosa en el Checkout. No se sobreescriben si ya vienen en $params.
        foreach ($this->merchantParams as $key => $value) {
            if ($value !== '' && $value !== null && ! isset($params[$key])) {
                $params[$key] = $value;
            }
        }

        // merchantTransactionId único por intento (mín. 8, máx. 255 alfanuméricos).
        $params['merchantTransactionId'] = $this->normalizeMerchantTransactionId($params['merchantTransactionId'] ?? null);

        return $this->send('post', $path, fn () => $this->client()->asForm()->post($this->url($path), $params), $params);
    }

    /**
     * Deja un merchantTransactionId estrictamente alfanumérico de 8-255 chars.
     * Si el valor provisto no cumple, genera uno nuevo único.
     */
    private function normalizeMerchantTransactionId(?string $value): string
    {
        $clean = preg_replace('/[^A-Za-z0-9]/', '', (string) $value);

        if (strlen($clean) < 8) {
            $clean = 'PATS'.now()->format('YmdHis').strtoupper(bin2hex(random_bytes(4)));
        }

        return substr($clean, 0, 255);
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     *
     * @throws ProsaTimeoutException
     */
    public function get(string $path, array $query = []): array
    {
        $query['entityId'] = $this->entityId;

        return $this->send('get', $path, fn () => $this->client()->get($this->url($path), $query), $query);
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     *
     * @throws ProsaTimeoutException
     */
    public function delete(string $path, array $query = []): array
    {
        $query['entityId'] = $this->entityId;

        return $this->send('delete', $path, fn () => $this->client()->delete($this->url($path), $query), $query);
    }

    private function client()
    {
        return Http::withToken($this->accessToken)
            ->acceptJson()
            ->timeout($this->timeout)
            ->connectTimeout($this->connectTimeout);
    }

    private function url(string $path): string
    {
        return $this->baseUrl.$path;
    }

    /**
     * Ejecuta la petición con un reintento ante timeout de conexión.
     *
     * @param  callable():\Illuminate\Http\Client\Response  $request
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     *
     * @throws ProsaTimeoutException
     */
    private function send(string $verb, string $path, callable $request, array $context): array
    {
        $attempts = 0;

        do {
            $attempts++;
            try {
                $response = $request();

                return (array) $response->json();
            } catch (ConnectionException $e) {
                Log::channel('prosa')->warning('Prosa connection timeout', [
                    'path' => $path,
                    'attempt' => $attempts,
                ]);

                if ($attempts > $this->retryTimes) {
                    throw new ProsaTimeoutException(
                        context: ['path' => $path, 'params' => $this->scrub($context)],
                        previous: $e,
                    );
                }

                usleep($this->retrySleepMs * 1000);
            }
        } while (true);
    }

    /**
     * Elimina datos sensibles de tarjeta antes de loggear/propagar contexto.
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    private function scrub(array $params): array
    {
        foreach (['card.number', 'card.cvv'] as $key) {
            if (isset($params[$key])) {
                $params[$key] = '***';
            }
        }

        return $params;
    }
}
