<?php

namespace App\Http\Controllers\Feenicia;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Feenicia\OneStepSaleRequest;
use App\Http\Requests\Feenicia\CashSaleRequest;
use App\Http\Requests\Feenicia\RecurringBillingRequest;
use App\Http\Requests\Feenicia\PostSaleRequest;
use App\Services\Feenicia\OneStepSaleService;
use App\Services\Feenicia\CashSaleService;
use App\Services\Feenicia\RecurringBillingService;
use App\Services\Feenicia\RefundService;
use App\Services\Feenicia\CancellationService;
use App\Services\Feenicia\ReversalService;
use App\Exceptions\Feenicia\FeeniciaException;
use App\Exceptions\Feenicia\FeeniciaTimeoutException;
use App\Models\FeeniciaTransaction;

class FeeniciaTransactionController extends Controller
{
    public function __construct(
        private readonly OneStepSaleService      $oneStepSaleService,
        private readonly CashSaleService         $cashSaleService,
        private readonly RecurringBillingService $recurringBillingService,
        private readonly RefundService           $refundService,
        private readonly CancellationService     $cancellationService,
        private readonly ReversalService         $reversalService,
    ) {}

    // ── Ventas ────────────────────────────────────────────────

    /**
     * POST /api/feenicia/sale/one-step
     */
    public function oneStepSale(OneStepSaleRequest $request): JsonResponse
    {
        try {
            $result = $this->oneStepSaleService->execute($request->toDTO());

            $tx = FeeniciaTransaction::fromFeeniciaResponse(
                type:     FeeniciaTransaction::TYPE_ONE_STEP_SALE,
                response: $result,
                extra:    [
                    'affiliation' => $request->input('affiliation'),
                    'amount'      => $request->input('amount'),
                ],
            );

            return response()->json([
                'success'       => true,
                'transactionId' => $result['transactionId'],
                'authnum'       => $result['authnum'],
                'approved'      => $result['approved'],
                'amount'        => $result['amount'],
                'card'          => $result['card'],
                'internalId'    => $tx->id,
            ]);

        } catch (FeeniciaTimeoutException $e) {
            $this->logTimeout(FeeniciaTransaction::TYPE_ONE_STEP_SALE, $request);
            try { $this->reversalService->executeFromTimeout($e); } catch (\Throwable) {}
            return $this->timeoutResponse();

        } catch (FeeniciaException $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * POST /api/feenicia/sale/cash
     */
    public function cashSale(CashSaleRequest $request): JsonResponse
    {
        try {
            $result = $this->cashSaleService->execute(
                cash:          $request->toCashDTO(),
                order:         $request->toOrderDTO(),
                sendReceiptTo: $request->input('sendReceiptTo'),
            );

            $tx = FeeniciaTransaction::fromFeeniciaResponse(
                type:     FeeniciaTransaction::TYPE_CASH_SALE,
                response: $result,
                extra:    [
                    'affiliation' => $request->input('affiliation'),
                    'amount'      => $request->input('amount'),
                    'order_id'    => (string) $result['orderId'],
                ],
            );

            return response()->json([
                'success'       => true,
                'orderId'       => $result['orderId'],
                'transactionId' => $result['transactionId'],
                'authnum'       => $result['authnum'],
                'approved'      => $result['approved'],
                'amount'        => $result['amount'],
                'tip'           => $result['tip'],
                'receiptId'     => $result['receiptId'],
                'internalId'    => $tx->id,
            ]);

        } catch (FeeniciaTimeoutException $e) {
            $this->logTimeout(FeeniciaTransaction::TYPE_CASH_SALE, $request);
            try { $this->reversalService->executeFromTimeout($e); } catch (\Throwable) {}
            return $this->timeoutResponse();

        } catch (FeeniciaException $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * POST /api/feenicia/sale/recurring
     */
    public function recurringSale(RecurringBillingRequest $request): JsonResponse
    {
        try {
            $result = $this->recurringBillingService->execute(
                billing:       $request->toBillingDTO(),
                order:         $request->toOrderDTO(),
                sendReceiptTo: $request->input('sendReceiptTo'),
            );

            $tx = FeeniciaTransaction::fromFeeniciaResponse(
                type:     FeeniciaTransaction::TYPE_RECURRING,
                response: $result,
                extra:    [
                    'affiliation' => $request->input('affiliation'),
                    'amount'      => $request->input('amount'),
                    'order_id'    => (string) $result['orderId'],
                ],
            );

            return response()->json([
                'success'       => true,
                'orderId'       => $result['orderId'],
                'transactionId' => $result['transactionId'],
                'authnum'       => $result['authnum'],
                'approved'      => $result['approved'],
                'amount'        => $result['amount'],
                'card'          => $result['card'],
                'receiptId'     => $result['receiptId'],
                'internalId'    => $tx->id,
            ]);

        } catch (FeeniciaTimeoutException $e) {
            $this->logTimeout(FeeniciaTransaction::TYPE_RECURRING, $request);
            try { $this->reversalService->executeFromTimeout($e); } catch (\Throwable) {}
            return $this->timeoutResponse();

        } catch (FeeniciaException $e) {
            return $this->errorResponse($e);
        }
    }

    // ── Post-venta ────────────────────────────────────────────

    /**
     * POST /api/feenicia/refund
     */
    public function refund(PostSaleRequest $request): JsonResponse
    {
        try {
            $result = $this->refundService->execute($request->toDTO());

            $originalTx = FeeniciaTransaction::byTransactionId(
                $request->input('transactionId')
            )->first();

            $tx = FeeniciaTransaction::fromFeeniciaResponse(
                type:     FeeniciaTransaction::TYPE_REFUND,
                response: $result,
                extra:    [
                    'affiliation'             => $request->input('affiliation'),
                    'amount'                  => $request->input('amount'),
                    'original_transaction_id' => $originalTx?->id,
                ],
            );

            $originalTx?->update(['status' => FeeniciaTransaction::STATUS_REFUNDED]);

            return response()->json([
                'success'    => true,
                'approved'   => $result['approved'] ?? true,
                'internalId' => $tx->id,
            ]);

        } catch (FeeniciaException $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * POST /api/feenicia/cancellation
     */
    public function cancellation(PostSaleRequest $request): JsonResponse
    {
        try {
            $result = $this->cancellationService->execute($request->toDTO());

            FeeniciaTransaction::byTransactionId($request->input('transactionId'))
                ->first()
                ?->update(['status' => FeeniciaTransaction::STATUS_CANCELLED]);

            return response()->json(['success' => true, 'code' => $result['responseCode']]);

        } catch (FeeniciaException $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * POST /api/feenicia/reversal
     */
    public function reversal(PostSaleRequest $request): JsonResponse
    {
        try {
            $result = $this->reversalService->execute($request->toDTO());

            FeeniciaTransaction::byTransactionId($request->input('transactionId'))
                ->first()
                ?->update(['status' => FeeniciaTransaction::STATUS_REVERSED]);

            return response()->json(['success' => true, 'code' => $result['responseCode']]);

        } catch (FeeniciaException $e) {
            return $this->errorResponse($e);
        }
    }

    // ── Helpers ───────────────────────────────────────────────

    private function logTimeout(string $type, $request): void
    {
        FeeniciaTransaction::create([
            'type'        => $type,
            'status'      => FeeniciaTransaction::STATUS_TIMEOUT,
            'affiliation' => $request->input('affiliation'),
            'amount'      => $request->input('amount'),
            'merchant'    => config('feenicia.merchant'),
            'approved'    => false,
        ]);
    }

    private function timeoutResponse(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error'   => 'Timeout al procesar la transacción. Se envió reverso automático.',
            'code'    => 'TIMEOUT',
        ], 504);
    }

    private function errorResponse(FeeniciaException $e): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error'   => $e->getMessage(),
            'code'    => $e->responseCode,
        ], $this->httpStatusFromCode($e->responseCode));
    }

    private function httpStatusFromCode(string $code): int
    {
        return match (true) {
            in_array($code, ['S001','S002','S003','S004','S005','A006']) => 401,
            in_array($code, ['A001','A002','A003'])                     => 422,
            in_array($code, ['05','51','54','33','14'])                  => 402,
            in_array($code, ['91','96','90'])                           => 503,
            default                                                     => 400,
        };
    }
}
