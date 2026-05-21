<?php

namespace App\Http\Controllers\Feenicia;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Feenicia\TokenizationService;
use App\DTO\Feenicia\Tokenization\GenerateTokenData;
use App\DTO\Feenicia\Tokenization\SaleTokenData;
use App\DTO\Feenicia\Tokenization\CancelCardData;
use App\DTO\Feenicia\Tokenization\UpdateCardData;
use App\DTO\Feenicia\Tokenization\DeleteCardData;
use App\DTO\Feenicia\Tokenization\TokenReversalData;
use App\DTO\Feenicia\Tokenization\TokenRefundData;
use App\Exceptions\Feenicia\FeeniciaException;
use App\Models\FeeniciaToken;

class FeeniciaTokenController extends Controller
{
    public function __construct(
        private readonly TokenizationService $tokenizationService,
    ) {}

    /**
     * Obtiene el userId actual.
     * TODO: reemplazar con $request->user()->id cuando tengas autenticación.
     */
    private function getCurrentUserId(Request $request): int
    {
        return $request->user()?->id ?? 1;
    }

    // ──────────────────────────────────────────────
    //  Tarjetas
    // ──────────────────────────────────────────────

    /**
     * GET /api/feenicia/token/cards
     */
    public function cards(Request $request): JsonResponse
    {
        $userId = $this->getCurrentUserId($request);

        $tokens = FeeniciaToken::active()
            ->forUser($userId)
            ->get()
            ->map(fn ($t) => [
                'id'          => $t->id,
                'displayName' => $t->displayName(),
                'brand'       => $t->card_brand,
                'last4'       => $t->card_last4,
                'product'     => $t->card_product,
                'expDate'     => $t->exp_date,
                'isDefault'   => $t->is_default,
            ]);

        return response()->json(['success' => true, 'cards' => $tokens]);
    }

    /**
     * POST /api/feenicia/token/generate
     */
    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'pan'            => ['required', 'string'],
            'cardholderName' => ['required', 'string'],
            'expDate'        => ['required', 'string', 'size:4'],
            'cvv2'           => ['required', 'string', 'min:3', 'max:4'],
            'affiliation'    => ['required', 'string', 'max:15'],
            'alias'          => ['nullable', 'string', 'max:50'],
            'asDefault'      => ['nullable', 'boolean'],
        ]);

        try {
            $token = $this->tokenizationService->generateToken(
                data: new GenerateTokenData(
                    pan:            $request->input('pan'),
                    cardholderName: $request->input('cardholderName'),
                    expDate:        $request->input('expDate'),
                    cvv2:           $request->input('cvv2'),
                    affiliation:    $request->input('affiliation'),
                    alias:          $request->input('alias'),
                ),
                userId:    $this->getCurrentUserId($request),
                asDefault: $request->boolean('asDefault', false),
            );

            return response()->json([
                'success'     => true,
                'tokenId'     => $token->id,
                'displayName' => $token->displayName(),
                'card'        => [
                    'brand'   => $token->card_brand,
                    'last4'   => $token->card_last4,
                    'product' => $token->card_product,
                ],
            ], 201);

        } catch (FeeniciaException $e) {
            return $this->errorResponse($e);
        }
    }

    // ──────────────────────────────────────────────
    //  Cobro con token
    // ──────────────────────────────────────────────

    /**
     * POST /api/feenicia/token/sale
     */
    public function sale(Request $request): JsonResponse
    {
        $request->validate([
            'tokenId'         => ['required', 'integer', 'exists:feenicia_tokens,id'],
            'amount'          => ['required', 'numeric', 'min:0.01'],
            'cvv2'            => ['required', 'string', 'min:3', 'max:4'],
            'transactionDate' => ['required', 'integer'],
            'tip'             => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            $tokenRecord = FeeniciaToken::active()
                ->forUser($this->getCurrentUserId($request))
                ->findOrFail($request->input('tokenId'));

            $result = $this->tokenizationService->saleToken(
                new SaleTokenData(
                    token:           $tokenRecord->token,
                    amount:          (float) $request->input('amount'),
                    affiliation:     $tokenRecord->affiliation,
                    transactionDate: (int) $request->input('transactionDate'),
                    cvv2:            $request->input('cvv2'),
                    tip:             (float) $request->input('tip', 0),
                )
            );

            return response()->json([
                'success'       => true,
                'transactionId' => $result['transactionId'],
                'authnum'       => $result['authnum'],
                'approved'      => $result['approved'],
                'amount'        => $result['amount'],
            ]);

        } catch (FeeniciaException $e) {
            return $this->errorResponse($e);
        }
    }

    // ──────────────────────────────────────────────
    //  Gestión de tarjetas
    // ──────────────────────────────────────────────

    /**
     * DELETE /api/feenicia/token/{id}
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $token = FeeniciaToken::active()
            ->forUser($this->getCurrentUserId($request))
            ->findOrFail($id);

        try {
            $this->tokenizationService->deleteCard(new DeleteCardData(
                token:       $token->token,
                affiliation: $token->affiliation,
            ));

            return response()->json(['success' => true]);

        } catch (FeeniciaException $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * PATCH /api/feenicia/token/{id}/default
     */
    public function setDefault(Request $request, int $id): JsonResponse
    {
        $token = FeeniciaToken::active()
            ->forUser($this->getCurrentUserId($request))
            ->findOrFail($id);

        $token->setAsDefault();

        return response()->json(['success' => true]);
    }

    // ──────────────────────────────────────────────
    //  Post-venta
    // ──────────────────────────────────────────────

    /**
     * POST /api/feenicia/token/reversal
     */
    public function reversal(Request $request): JsonResponse
    {
        $request->validate([
            'tokenId'         => ['required', 'integer', 'exists:feenicia_tokens,id'],
            'amount'          => ['required', 'numeric'],
            'transactionDate' => ['required', 'integer'],
            'transactionId'   => ['required', 'string'],
            'authnum'         => ['required', 'string'],
        ]);

        $token = FeeniciaToken::forUser($this->getCurrentUserId($request))
            ->findOrFail($request->input('tokenId'));

        try {
            $result = $this->tokenizationService->reversalSale(new TokenReversalData(
                token:           $token->token,
                affiliation:     $token->affiliation,
                amount:          (float) $request->input('amount'),
                transactionDate: (int) $request->input('transactionDate'),
                transactionId:   $request->input('transactionId'),
                authnum:         $request->input('authnum'),
            ));

            return response()->json(['success' => true, 'code' => $result['responseCode']]);

        } catch (FeeniciaException $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * POST /api/feenicia/token/refund
     */
    public function refund(Request $request): JsonResponse
    {
        $request->validate([
            'tokenId'         => ['required', 'integer', 'exists:feenicia_tokens,id'],
            'amount'          => ['required', 'numeric'],
            'transactionDate' => ['required', 'integer'],
            'transactionId'   => ['required', 'string'],
            'authnum'         => ['required', 'string'],
        ]);

        $token = FeeniciaToken::forUser($this->getCurrentUserId($request))
            ->findOrFail($request->input('tokenId'));

        try {
            $result = $this->tokenizationService->refundTx(new TokenRefundData(
                token:           $token->token,
                affiliation:     $token->affiliation,
                amount:          (float) $request->input('amount'),
                transactionDate: (int) $request->input('transactionDate'),
                transactionId:   $request->input('transactionId'),
                authnum:         $request->input('authnum'),
            ));

            return response()->json(['success' => true, 'code' => $result['responseCode']]);

        } catch (FeeniciaException $e) {
            return $this->errorResponse($e);
        }
    }

    // ── Helper ────────────────────────────────────

    private function errorResponse(FeeniciaException $e): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error'   => $e->getMessage(),
            'code'    => $e->responseCode,
        ], 400);
    }
}