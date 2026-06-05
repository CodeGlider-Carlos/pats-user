<?php

namespace App\Http\Controllers\Prosa;

use App\Http\Controllers\Controller;
use App\Models\ProsaTransaction;
use App\Services\Prosa\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Receptor de notificaciones (webhooks) de OPPWA.
 *
 * OPPWA cifra el cuerpo con AES-256-GCM. La llave (hex) se configura en
 * `prosa.webhook.secret`; el IV y el authentication tag viajan en los headers
 * `X-Initialization-Vector` y `X-Authentication-Tag`.
 *
 * Sólo registra/actualiza la transacción: el cobro server-to-server ya es
 * síncrono, el webhook sirve para conciliación y estados diferidos.
 */
class ProsaWebhookController extends Controller
{
    /**
     * POST /api/prosa/webhook
     */
    public function receive(Request $request): JsonResponse
    {
        $payload = $this->decrypt($request);

        if ($payload === null) {
            return response()->json(['received' => false], 400);
        }

        $resource = $payload['payload'] ?? $payload;
        $normalized = PaymentService::normalize($resource);

        if ($normalized['paymentId']) {
            ProsaTransaction::updateOrCreate(
                ['payment_id' => $normalized['paymentId']],
                [
                    'payment_type'       => $resource['paymentType'] ?? null,
                    'amount'             => $normalized['amount'],
                    'currency'           => $normalized['currency'] ?: config('prosa.currency'),
                    'result_code'        => $normalized['resultCode'],
                    'result_description' => $normalized['resultDescription'],
                    'brand'              => $normalized['brand'],
                    'last4'              => $normalized['last4'],
                    'registration_id'    => $normalized['registrationId'],
                    'status'             => $normalized['approved'] ? 'approved' : ($normalized['pending'] ? 'pending' : 'rejected'),
                    'origen'             => 'webhook',
                    'raw_response'       => $resource,
                ],
            );
        }

        return response()->json(['received' => true]);
    }

    /**
     * Descifra el cuerpo del webhook. Devuelve el arreglo decodificado o null.
     *
     * @return array<string, mixed>|null
     */
    private function decrypt(Request $request): ?array
    {
        $secret = (string) config('prosa.webhook.secret');

        if ($secret === '') {
            Log::warning('Prosa webhook recibido sin secret configurado.');

            return null;
        }

        $ivHex  = $request->header('X-Initialization-Vector');
        $tagHex = $request->header('X-Authentication-Tag');
        $body   = json_decode($request->getContent(), true);
        $cipherHex = $body['encryptedBody'] ?? null;

        if (! $ivHex || ! $tagHex || ! $cipherHex) {
            Log::warning('Prosa webhook con headers/cuerpo incompletos.');

            return null;
        }

        $plain = openssl_decrypt(
            hex2bin($cipherHex),
            'aes-256-gcm',
            hex2bin($secret),
            OPENSSL_RAW_DATA,
            hex2bin($ivHex),
            hex2bin($tagHex),
        );

        if ($plain === false) {
            Log::warning('Prosa webhook: fallo al descifrar el cuerpo.');

            return null;
        }

        return json_decode($plain, true);
    }
}
