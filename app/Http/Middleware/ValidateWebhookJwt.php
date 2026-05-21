<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

/**
 * Valida el JWT que Feenicia envía en el header Authorization de cada webhook.
 *
 * Proceso de validación (documentado en el manual de Notificaciones):
 *  1. Extraer el JWT del header Authorization: Bearer {token}
 *  2. Decodificar el payload del JWT (sin verificar aún)
 *  3. Extraer el campo 'signed' del payload
 *  4. Calcular HMAC-SHA256 del body JSON recibido usando el webhook_secret
 *  5. Comparar el hash calculado con el 'signed' del payload
 *  6. Verificar que el JWT esté firmado con HS384 y el mismo secret
 *
 * Si algo falla → 401. Si todo coincide → continuar.
 */
class ValidateWebhookJwt
{
    public function handle(Request $request, Closure $next): mixed
    {
        $authHeader = $request->header('Authorization', '');

        if (!str_starts_with($authHeader, 'Bearer ')) {
            Log::channel('feenicia')->warning('Webhook: header Authorization ausente o inválido');
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $jwt    = substr($authHeader, 7);
        $secret = config('feenicia.webhook.secret');

        if (empty($secret)) {
            Log::channel('feenicia')->error('Webhook: FEENICIA_WEBHOOK_SECRET no configurado');
            return response()->json(['error' => 'Server misconfiguration'], 500);
        }

        try {
            $this->validateJwt($jwt, $secret, $request->getContent());
        } catch (\RuntimeException $e) {
            Log::channel('feenicia')->warning('Webhook: JWT inválido — ' . $e->getMessage(), [
                'ip' => $request->ip(),
            ]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }

    // ──────────────────────────────────────────────
    //  Validación interna
    // ──────────────────────────────────────────────

    /**
     * Valida la firma del JWT y verifica que el campo 'signed'
     * coincida con el HMAC-SHA256 del body recibido.
     *
     * @throws \RuntimeException  Si cualquier parte de la validación falla
     */
    private function validateJwt(string $jwt, string $secret, string $rawBody): void
    {
        $parts = explode('.', $jwt);

        if (count($parts) !== 3) {
            throw new \RuntimeException('JWT malformado — no tiene 3 partes');
        }

        [$headerB64, $payloadB64, $signatureB64] = $parts;

        // ── Paso 1: Decodificar header y verificar algoritmo ──
        $header = json_decode($this->base64UrlDecode($headerB64), true);

        if (($header['alg'] ?? '') !== 'HS384') {
            throw new \RuntimeException('Algoritmo JWT inesperado: ' . ($header['alg'] ?? 'null'));
        }

        // ── Paso 2: Verificar firma del JWT con HS384 ──
        // El secret se encodea en Base64 antes de usarse (según el manual)
        $secretEncoded   = base64_encode($secret);
        $expectedSig     = $this->hmac('sha384', "{$headerB64}.{$payloadB64}", $secretEncoded);
        $receivedSig     = $this->base64UrlDecode($signatureB64);

        if (!hash_equals($expectedSig, $receivedSig)) {
            throw new \RuntimeException('Firma JWT inválida');
        }

        // ── Paso 3: Decodificar payload ──
        $payload = json_decode($this->base64UrlDecode($payloadB64), true);

        if (!isset($payload['signed'])) {
            throw new \RuntimeException("Campo 'signed' ausente en el payload del JWT");
        }

        // ── Paso 4: Verificar que 'signed' coincida con HMAC-SHA256 del body ──
        $expectedSigned = hash_hmac('sha256', $rawBody, $secret);

        if (!hash_equals($expectedSigned, $payload['signed'])) {
            throw new \RuntimeException("Campo 'signed' no coincide con el body recibido");
        }
    }

    /**
     * Calcula HMAC y devuelve bytes binarios (para comparar con la firma del JWT).
     */
    private function hmac(string $algo, string $data, string $key): string
    {
        return hash_hmac($algo, $data, $key, true); // true = raw binary
    }

    /**
     * Decodifica Base64URL a string.
     * JWT usa Base64URL (sin padding, con - y _ en lugar de + y /).
     */
    private function base64UrlDecode(string $input): string
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $input .= str_repeat('=', 4 - $remainder);
        }

        return base64_decode(strtr($input, '-_', '+/'));
    }
}
