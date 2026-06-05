<?php

namespace App\Http\Controllers\Prosa;

use App\DTO\Prosa\CardData;
use App\Exceptions\Prosa\ProsaException;
use App\Exceptions\Prosa\ProsaTimeoutException;
use App\Http\Controllers\Controller;
use App\Models\ProsaToken;
use App\Models\ProsaTransaction;
use App\Services\Prosa\BackofficeService;
use App\Services\Prosa\TokenizationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Gestión de tarjetas tokenizadas (cards on file) y cobros con token.
 *
 * Reemplaza a FeeniciaTokenController (módulo Balder).
 */
class ProsaTokenController extends Controller
{
    public function __construct(
        private readonly TokenizationService $tokenizationService,
        private readonly BackofficeService $backofficeService,
    ) {}

    private function currentUserId(Request $request): int
    {
        // El portal de pagos usa el guard 'pasaporte' (session); probar primero.
        return $request->user('pasaporte')?->getKey()
            ?? $request->user()?->getKey()
            ?? 0;
    }

    /**
     * GET /api/prosa/token/cards
     */
    public function cards(Request $request): JsonResponse
    {
        $tokens = ProsaToken::active()
            ->forUser($this->currentUserId($request))
            ->orderByDesc('is_default')
            ->get()
            ->map(fn (ProsaToken $t) => [
                'id'          => $t->id,
                'displayName' => $t->displayName(),
                'brand'       => $t->card_brand,
                'last4'       => $t->card_last4,
                'expDate'     => $t->expDate(),
                'isDefault'   => $t->is_default,
                'alias'       => $t->alias,
            ]);

        return response()->json(['success' => true, 'cards' => $tokens]);
    }

    /**
     * POST /api/prosa/token/generate
     */
    public function generate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'pan'            => ['required', 'string'],
            'cardholderName' => ['required', 'string', 'max:128'],
            'expMonth'       => ['required', 'string', 'max:2'],
            'expYear'        => ['required', 'string', 'max:4'],
            'cvv2'           => ['required', 'string', 'min:3', 'max:4'],
            'alias'          => ['nullable', 'string', 'max:50'],
            'asDefault'      => ['nullable', 'boolean'],
        ]);

        $card = CardData::fromForm(
            number: $data['pan'],
            holder: $data['cardholderName'],
            expMonth: $data['expMonth'],
            expYear: $data['expYear'],
            cvv: $data['cvv2'],
        );

        try {
            $registration = $this->tokenizationService->register($card);
        } catch (ProsaException $e) {
            return $this->errorResponse($e);
        }

        $token = ProsaToken::create([
            'user_id'         => $this->currentUserId($request),
            'registration_id' => $registration['registrationId'],
            'alias'           => $data['alias'] ?? null,
            'card_brand'      => $registration['brand'],
            'card_bin'        => $registration['bin'],
            'card_last4'      => $registration['last4'],
            'cardholder_name' => $card->holder,
            'exp_month'       => $card->expiryMonth,
            'exp_year'        => $card->expiryYear,
            'status'          => 'active',
            'is_default'      => false,
        ]);

        if ($request->boolean('asDefault')) {
            $token->setAsDefault();
        }

        return response()->json([
            'success'     => true,
            'tokenId'     => $token->id,
            'displayName' => $token->displayName(),
            'card'        => ['brand' => $token->card_brand, 'last4' => $token->card_last4],
        ], 201);
    }

    /**
     * POST /api/prosa/token/sale
     */
    public function sale(Request $request): JsonResponse
    {
        $data = $request->validate([
            'tokenId' => ['required', 'integer', 'exists:prosa_tokens,id'],
            'amount'  => ['required', 'numeric', 'min:0.01'],
            'cvv2'    => ['nullable', 'string', 'min:3', 'max:4'],
        ]);

        $userId = $this->currentUserId($request);

        $token = ProsaToken::active()
            ->forUser($userId)
            ->find($data['tokenId']);

        if (! $token) {
            Log::warning('ProsaTokenController::sale — token not found', [
                'tokenId' => $data['tokenId'],
                'userId'  => $userId,
            ]);

            return response()->json([
                'success' => false,
                'error'   => 'Tarjeta no encontrada o no pertenece a tu cuenta.',
                'code'    => 'TOKEN_NOT_FOUND',
            ], 404);
        }

        try {
            $result = $this->tokenizationService->chargeToken(
                registrationId: $token->registration_id,
                amount: (float) $data['amount'],
                cvv: $data['cvv2'] ?? null,
                currency: config('prosa.currency'),
                merchantTransactionId: 'PATS-' . now()->format('YmdHis'),
            );
        } catch (ProsaTimeoutException $e) {
            Log::error('ProsaTokenController::sale — timeout', ['tokenId' => $token->id]);

            return response()->json([
                'success' => false,
                'error'   => 'Timeout al procesar el pago. Verifica el estado antes de reintentar.',
                'code'    => 'TIMEOUT',
            ], 504);
        } catch (ProsaException $e) {
            Log::warning('ProsaTokenController::sale — gateway rejected', [
                'tokenId'    => $token->id,
                'resultCode' => $e->resultCode,
                'message'    => $e->getMessage(),
            ]);

            // 100.150.203 — the stored registration was never valid (common with
            // Amex when the acquirer doesn't support Amex tokenization).
            // Remove it so the user doesn't see a card they can never charge.
            if ($e->resultCode === '100.150.203') {
                $token->update(['status' => 'deleted', 'is_default' => false]);
                $token->delete();

                return response()->json([
                    'success'       => false,
                    'error'         => 'Esta tarjeta no puede usarse para pagos guardados (el registro fue rechazado por la red). Fue eliminada de tus tarjetas guardadas. Intenta pagar directamente con la tarjeta.',
                    'code'          => $e->resultCode,
                    'cardRemoved'   => true,
                ], 400);
            }

            return $this->errorResponse($e);
        }

        ProsaTransaction::create([
            'user_id'            => $userId,
            'registration_id'    => $token->registration_id,
            'payment_id'         => $result['paymentId'],
            'payment_type'       => 'DB',
            'amount'             => $result['amount'] ?? $data['amount'],
            'currency'           => config('prosa.currency'),
            'result_code'        => $result['resultCode'],
            'result_description' => $result['resultDescription'],
            'brand'              => $token->card_brand,
            'last4'              => $token->card_last4,
            'status'             => 'approved',
            'origen'             => 'token_sale',
            'raw_response'       => $result['raw'],
        ]);

        return response()->json([
            'success'       => true,
            'transactionId' => $result['paymentId'],
            'authnum'       => $result['paymentId'],
            'approved'      => true,
            'amount'        => $result['amount'] ?? $data['amount'],
        ]);
    }

    /**
     * DELETE /api/prosa/token/{id}
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $token = ProsaToken::active()
            ->forUser($this->currentUserId($request))
            ->findOrFail($id);

        try {
            $this->tokenizationService->delete($token->registration_id);
        } catch (\Throwable) {
            // Continuamos: marcamos la tarjeta como inactiva localmente aunque
            // OPPWA ya la hubiera eliminado o no responda.
        }

        $token->update(['status' => 'deleted', 'is_default' => false]);
        $token->delete();

        return response()->json(['success' => true]);
    }

    /**
     * PATCH /api/prosa/token/{id}/default
     */
    public function setDefault(Request $request, int $id): JsonResponse
    {
        $token = ProsaToken::active()
            ->forUser($this->currentUserId($request))
            ->findOrFail($id);

        $token->setAsDefault();

        return response()->json(['success' => true]);
    }

    /**
     * POST /api/prosa/token/refund
     */
    public function refund(Request $request): JsonResponse
    {
        $data = $request->validate([
            'paymentId' => ['required', 'string'],
            'amount'    => ['required', 'numeric', 'min:0.01'],
        ]);

        try {
            $result = $this->backofficeService->refund(
                $data['paymentId'],
                (float) $data['amount'],
                config('prosa.currency'),
            );
        } catch (ProsaException $e) {
            return $this->errorResponse($e);
        }

        return response()->json(['success' => true, 'code' => $result['resultCode']]);
    }

    /**
     * POST /api/prosa/token/reversal
     */
    public function reversal(Request $request): JsonResponse
    {
        $data = $request->validate([
            'paymentId' => ['required', 'string'],
        ]);

        try {
            $result = $this->backofficeService->reversal($data['paymentId']);
        } catch (ProsaException $e) {
            return $this->errorResponse($e);
        }

        return response()->json(['success' => true, 'code' => $result['resultCode']]);
    }

    private function errorResponse(ProsaException $e): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error'   => $e->getMessage(),
            'code'    => $e->resultCode,
        ], 400);
    }
}
