<?php

namespace App\Http\Controllers\Prosa;

use App\Http\Controllers\Controller;
use App\Models\ProsaPendingCheckout;
use App\Models\ProsaTransaction;
use App\Services\Prosa\Checkout\CheckoutManager;
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
    public function __construct(
        private readonly CheckoutManager $checkoutManager,
    ) {}

    /**
     * POST /api/prosa/webhook
     */
    public function receive(Request $request): JsonResponse
    {
        $payload = $this->decrypt($request);

        if ($payload === null) {
            return response()->json(['received' => false], 400);
        }

        $resource   = $payload['payload'] ?? $payload;
        $normalized = PaymentService::normalize($resource);

        if (! $normalized['paymentId']) {
            return response()->json(['received' => true]);
        }

        // ── Actualizar / crear la transacción ────────────────────────────────
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

        // ── Completar checkout pendiente (OXXO u otro diferido) ─────────────
        // Sólo cuando el pago queda aprobado y hay un checkout vinculado.
        if ($normalized['approved']) {
            $checkout = ProsaPendingCheckout::where('payment_id', $normalized['paymentId'])
                ->where('status', ProsaPendingCheckout::STATUS_PENDING)
                ->first();

            if ($checkout) {
                try {
                    $this->checkoutManager->finish($checkout, $normalized);
                } catch (\Throwable $e) {
                    Log::error('Webhook: error al completar checkout', [
                        'checkout_id' => $checkout->id,
                        'payment_id'  => $normalized['paymentId'],
                        'error'       => $e->getMessage(),
                    ]);
                }
            }
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
