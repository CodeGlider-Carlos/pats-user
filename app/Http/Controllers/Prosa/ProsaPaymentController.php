<?php

namespace App\Http\Controllers\Prosa;

use App\DTO\Prosa\CardData;
use App\DTO\Prosa\ChargeData;
use App\DTO\Prosa\ThreeDSData;
use App\Exceptions\Prosa\ProsaException;
use App\Exceptions\Prosa\ProsaTimeoutException;
use App\Http\Controllers\Controller;
use App\Models\ProsaPendingCheckout;
use App\Models\ProsaTransaction;
use App\Services\Prosa\BackofficeService;
use App\Services\Prosa\Checkout\CheckoutManager;
use App\Services\Prosa\Checkout\PagosCheckout;
use App\Services\Prosa\PaymentService;
use App\Services\Prosa\RecurringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Endpoints de transacción para el portal de pagos (payment.blade.php).
 *
 * Equivalente a la antigua FeeniciaTransactionController: ejecuta el cobro y
 * registra una {@see ProsaTransaction}. La actualización del pasaporte /
 * comisiones vive en los controladores de orden (Pagos/Adquirir).
 */
class ProsaPaymentController extends Controller
{
    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly RecurringService $recurringService,
        private readonly BackofficeService $backofficeService,
        private readonly CheckoutManager $checkoutManager,
    ) {}

    /**
     * POST /api/prosa/sale/one-step
     *
     * Inicia el cobro con 3-D Secure. Devuelve uno de tres estados:
     *  - approved:  pago aprobado; `redirectUrl` para recargar la página.
     *  - challenge: requiere reto; `challenge` con la redirección al ACS.
     *  - declined:  rechazado.
     */
    public function oneStepSale(Request $request): JsonResponse
    {
        $data = $request->validate([
            'amount'         => ['required', 'numeric', 'min:0.01'],
            'pan'            => ['required', 'string'],
            'cardholderName' => ['required', 'string', 'max:128'],
            'cvv2'           => ['required', 'string', 'min:3', 'max:4'],
            'expMonth'       => ['required', 'string', 'max:2'],
            'expYear'        => ['required', 'string', 'max:4'],
            'email'          => ['nullable', 'email'],
            'browser'        => ['nullable', 'array'],
            'saveCard'       => ['nullable', 'boolean'],
            'alias'          => ['nullable', 'string', 'max:50'],
        ]);

        $saveCard = (bool) ($data['saveCard'] ?? false);

        $card = CardData::fromForm(
            number: $data['pan'],
            holder: $data['cardholderName'],
            expMonth: $data['expMonth'],
            expYear: $data['expYear'],
            cvv: $data['cvv2'],
        );

        // Resolver user_id probando primero el guard 'pasaporte'.
        $userId = $request->user('pasaporte')?->getKey() ?? $request->user()?->getKey();

        $mtx = $this->reference();
        
        $checkout = ProsaPendingCheckout::create([
            'merchant_transaction_id' => $mtx,
            'flow'                    => PagosCheckout::FLOW,
            'status'                  => ProsaPendingCheckout::STATUS_PENDING,
            'user_id'                 => $userId,
            'amount'                  => $data['amount'],
            'payload'                 => [
                'origen'   => 'one_step',
                'brand'    => $card->brand(),
                'last4'    => $card->last4(),
                'saveCard' => $saveCard,
                'alias'    => $data['alias'] ?? null,
                'holder'   => $card->holder,
                'expMonth' => $card->expiryMonth,
                'expYear'  => $card->expiryYear,
            ],
        ]);

        $threeDs = ThreeDSData::fromRequest(
            request: $request,
            shopperResultUrl: route('prosa.3ds.return', ['mtx' => $mtx]),
            email: $data['email'] ?? null,
            givenName: $card->holder,
            browser: $data['browser'] ?? null,
        );

        try {
            $result = $this->paymentService->initiate(new ChargeData(
                card:               $card,
                amount:             (float) $data['amount'],
                currency:           config('prosa.currency'),
                merchantTransactionId: $mtx,
                createRegistration: $saveCard,  // <-- OPPWA tokeniza la tarjeta junto con el pago
            ), $threeDs);
        } catch (ProsaTimeoutException $e) {
            $checkout->update(['status' => ProsaPendingCheckout::STATUS_DECLINED]);
            $this->logTimeout('one_step', $data['amount']);

            return $this->timeoutResponse();
        }

        return $this->respondToInitiation($checkout, $result);
    }

    /**
     * Convierte el resultado de PaymentService::initiate en una respuesta JSON
     * uniforme y, en el camino frictionless, finaliza el flujo de negocio.
     *
     * @param  array<string, mixed>  $result
     */
    private function respondToInitiation(ProsaPendingCheckout $checkout, array $result): JsonResponse
    {
        $checkout->update(['payment_id' => $result['paymentId'] ?? null]);

        if ($result['status'] === 'challenge') {
            $checkout->update([
                'status'   => ProsaPendingCheckout::STATUS_CHALLENGE,
                'redirect' => $result['redirect'],
            ]);

            return response()->json([
                'success'   => true,
                'status'    => 'challenge',
                'challenge' => $result['redirect'],
            ]);
        }

        if ($result['status'] === 'approved') {
            $redirectUrl = $this->checkoutManager->finish($checkout, $result);

            return response()->json([
                'success'     => true,
                'status'      => 'approved',
                'redirectUrl' => $redirectUrl,
                'paymentId'   => $result['paymentId'],
            ]);
        }

        $checkout->update([
            'status'             => ProsaPendingCheckout::STATUS_DECLINED,
            'result_code'        => $result['resultCode'] ?? null,
            'result_description' => $result['resultDescription'] ?? null,
        ]);

        return response()->json([
            'success' => false,
            'status'  => 'declined',
            'error'   => $result['resultDescription'] ?: 'El pago fue rechazado.',
            'code'    => $result['resultCode'] ?? '',
        ], 400);
    }

    /**
     * POST /api/prosa/sale/recurring
     *
     * Cobro recurrente INICIAL (CIT). Se envía con parámetros 3DS y
     * `createRegistration=true`. Devuelve approved | challenge | declined.
     */
    public function recurringSale(Request $request): JsonResponse
    {
        $data = $request->validate([
            'amount'         => ['required', 'numeric', 'min:0.01'],
            'pan'            => ['required', 'string'],
            'cardholderName' => ['required', 'string', 'max:128'],
            'cvv2'           => ['required', 'string', 'min:3', 'max:4'],
            'expMonth'       => ['required', 'string', 'max:2'],
            'expYear'        => ['required', 'string', 'max:4'],
            'email'          => ['nullable', 'email'],
            'browser'        => ['nullable', 'array'],
        ]);

        $card = CardData::fromForm(
            number:   $data['pan'],
            holder:   $data['cardholderName'],
            expMonth: $data['expMonth'],
            expYear:  $data['expYear'],
            cvv:      $data['cvv2'],
        );

        $mtx = $this->reference();

        $checkout = ProsaPendingCheckout::create([
            'merchant_transaction_id' => $mtx,
            'flow'                    => PagosCheckout::FLOW,
            'status'                  => ProsaPendingCheckout::STATUS_PENDING,
            'user_id'                 => $request->user('pasaporte')?->getKey() ?? $request->user()?->getKey(),
            'amount'                  => $data['amount'],
            'payload'                 => [
                'origen' => 'recurring',
                'brand'  => $card->brand(),
                'last4'  => $card->last4(),
            ],
        ]);

        $threeDs = ThreeDSData::fromRequest(
            request: $request,
            shopperResultUrl: route('prosa.3ds.return', ['mtx' => $mtx]),
            email: $data['email'] ?? null,
            givenName: $card->holder,
            browser: $data['browser'] ?? null,
        );

        try {
            $result = $this->recurringService->startRecurring(
                card:                  $card,
                amount:                (float) $data['amount'],
                currency:              config('prosa.currency'),
                merchantTransactionId: $mtx,
                extraParams:           $threeDs->toParams(),
            );
        } catch (ProsaTimeoutException $e) {
            $checkout->update(['status' => ProsaPendingCheckout::STATUS_DECLINED]);
            $this->logTimeout('recurring', $data['amount']);

            return $this->timeoutResponse();
        }

        return $this->respondToInitiation($checkout, $result);
    }

    /**
     * POST /api/prosa/sale/cash
     *
     * Prosa/OPPWA no procesa efectivo: se registra un movimiento manual
     * pendiente que el back-office concilia.
     */
    public function cashSale(Request $request): JsonResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $tx = ProsaTransaction::create([
            'user_id'      => $request->user()?->id,
            'payment_type' => 'CASH',
            'amount'       => $data['amount'],
            'currency'     => config('prosa.currency'),
            'status'       => 'pending',
            'origen'       => 'cash',
        ]);

        return response()->json([
            'success'       => true,
            'transactionId' => $tx->id,
            'amount'        => $data['amount'],
            'status'        => 'pending',
            'internalId'    => $tx->id,
        ]);
    }

    /**
     * POST /api/prosa/refund
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

        ProsaTransaction::create([
            'user_id'           => $request->user()?->id,
            'payment_id'        => $result['paymentId'],
            'payment_type'      => 'RF',
            'amount'            => $data['amount'],
            'currency'          => config('prosa.currency'),
            'result_code'       => $result['resultCode'],
            'result_description' => $result['resultDescription'],
            'status'            => 'refunded',
            'origen'            => 'refund',
            'raw_response'      => $result['raw'],
        ]);

        return response()->json(['success' => true, 'code' => $result['resultCode']]);
    }

    /**
     * POST /api/prosa/reversal
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

        ProsaTransaction::create([
            'user_id'           => $request->user()?->id,
            'payment_id'        => $result['paymentId'],
            'payment_type'      => 'RV',
            'currency'          => config('prosa.currency'),
            'result_code'       => $result['resultCode'],
            'result_description' => $result['resultDescription'],
            'status'            => 'reversed',
            'origen'            => 'reversal',
            'raw_response'      => $result['raw'],
        ]);

        return response()->json(['success' => true, 'code' => $result['resultCode']]);
    }

    // ── Helpers ───────────────────────────────────────────────

    private function reference(): string
    {
        return 'PATS-' . now()->format('YmdHis') . '-' . strtoupper(substr(md5(uniqid()), 0, 8));
    }

    /**
     * @param  array<string, mixed>  $result
     */
    private function logTransaction(string $origen, array $result, CardData $card): ProsaTransaction
    {
        return ProsaTransaction::create([
            'user_id'            => request()->user('pasaporte')?->getKey() ?? request()->user()?->getKey(),
            'registration_id'    => $result['registrationId'] ?? null,
            'payment_id'         => $result['paymentId'],
            'payment_type'       => 'DB',
            'amount'             => $result['amount'],
            'currency'           => $result['currency'] ?: config('prosa.currency'),
            'result_code'        => $result['resultCode'],
            'result_description' => $result['resultDescription'],
            'brand'              => $result['brand'] ?: $card->brand(),
            'last4'              => $result['last4'] ?? $card->last4(),
            'status'             => 'approved',
            'origen'             => $origen,
            'raw_response'       => $result['raw'],
        ]);
    }

    private function logTimeout(string $origen, mixed $amount): void
    {
        ProsaTransaction::create([
            'user_id'      => request()->user('pasaporte')?->getKey() ?? request()->user()?->getKey(),
            'payment_type' => 'DB',
            'amount'       => $amount,
            'currency'     => config('prosa.currency'),
            'status'       => 'timeout',
            'origen'       => $origen,
        ]);
    }

    private function timeoutResponse(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error'   => 'Timeout al procesar la transacción. Verifica el estado antes de reintentar.',
            'code'    => 'TIMEOUT',
        ], 504);
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
