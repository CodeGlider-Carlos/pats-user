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
use App\Services\Prosa\Checkout\PagosRecurringCheckout;
use App\Services\Prosa\OxxoService;
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
        private readonly OxxoService $oxxoService,
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
            'amount' => ['required', 'numeric', 'min:0.01'],
            'pan' => ['required', 'string'],
            'cardholderName' => ['required', 'string', 'max:128'],
            'cvv2' => ['required', 'string', 'min:3', 'max:4'],
            'expMonth' => ['required', 'string', 'max:2'],
            'expYear' => ['required', 'string', 'max:4'],
            'email' => ['nullable', 'email'],
            'browser' => ['nullable', 'array'],
            'saveCard' => ['nullable', 'boolean'],
            'alias' => ['nullable', 'string', 'max:50'],
            'billing' => ['nullable', 'array'],
            'billing.street1' => ['nullable', 'string', 'max:100'],
            'billing.city' => ['nullable', 'string', 'max:50'],
            'billing.state' => ['nullable', 'string', 'max:50'],
            'billing.postcode' => ['nullable', 'string', 'max:30'],
            'billing.country' => ['nullable', 'string', 'max:3'],
            'frecuencia' => ['required', 'in:MENSUAL,ANUAL'],
            'meses' => ['required', 'integer', 'min:1'],
            'id_tipo_precio' => ['required', 'integer'],
            'monto_membresia' => ['nullable', 'numeric', 'min:0'],
            'recargo' => ['nullable', 'numeric', 'min:0'],
            // Datos del titular, sólo se usan si aún no existe pasaporte (ALTA_PATS).
            'curp_usuario' => ['nullable', 'string', 'max:18'],
            'nombre_usuario' => ['nullable', 'string', 'max:120'],
            'apellido_pa' => ['nullable', 'string', 'max:80'],
            'apellido_ma' => ['nullable', 'string', 'max:80'],
            'fecha_nacimiento' => ['nullable', 'date'],
        ]);

        $saveCard = (bool) ($data['saveCard'] ?? false);

        $card = CardData::fromForm(
            number: $data['pan'],
            holder: $data['cardholderName'],
            expMonth: $data['expMonth'],
            expYear: $data['expYear'],
            cvv: $data['cvv2'],
        );

        $user = $request->user('pasaporte') ?? $request->user();
        $userId = $user?->getKey();
        $correo = $user?->correo_usuario ?? ($data['email'] ?? '');

        $pasaporte = \Illuminate\Support\Facades\DB::table('pats_pasaportes')
            ->where('correo', $correo)
            ->where('activo', 1)
            ->orderBy('fecha_alta', 'desc')
            ->first();

        $operacion = $pasaporte ? 'RENOVACION_PATS' : 'ALTA_PATS';

        $ahora = \Carbon\Carbon::now();
        $referencia = 'PATS-'.$ahora->format('YmdHis').'-'.strtoupper(substr(md5(uniqid()), 0, 8));
        $folio = 'ORD-'.$ahora->format('Ymd').'-'.strtoupper(substr(md5(uniqid()), 0, 6));

        $idOrden = \Illuminate\Support\Facades\DB::table('pats_ordenes_pago')->insertGetId([
            'folio_orden' => $folio,
            'referencia_pago' => $referencia,
            'tipo_origen' => 'PORTAL_CLIENTE',
            'origen_checkout' => 'PORTAL_CLIENTE',
            'id_distribuidor' => $pasaporte->id_distribuidor ?? 1,
            'id_franquicia' => $pasaporte->id_franquicia ?? 1,
            'id_pasaporte' => $pasaporte->id_pasaporte ?? null,
            'correo_usuario_pats' => $correo,
            'id_tipo_precio' => $data['id_tipo_precio'],
            'tipo_operacion' => $operacion,
            'frecuencia' => $data['frecuencia'],
            'monto_orden' => $data['amount'],
            'monto_nominal_base' => $data['amount'],
            'monto_extra_recargo' => 0.00,
            'moneda' => 'MXN',
            'estatus_orden' => 'PENDIENTE',
            'estatus_pago' => 'PENDIENTE',
            'proveedor_pasarela' => 'PROSA',
            'user_creo' => $correo,
            'fecha_orden' => $ahora,
            'created_at' => $ahora,
            'updated_at' => $ahora,
        ]);

        $mtx = $this->reference();

        $checkout = ProsaPendingCheckout::create([
            'merchant_transaction_id' => $mtx,
            'flow' => PagosCheckout::FLOW,
            'status' => ProsaPendingCheckout::STATUS_PENDING,
            'user_id' => $userId,
            'amount' => $data['amount'],
            'payload' => [
                'origen' => 'one_step',
                'id_orden' => $idOrden,
                'referencia' => $referencia,
                'folio' => $folio,
                'operacion' => $operacion,
                'correo_usuario' => $correo,
                'frecuencia' => $data['frecuencia'],
                'meses' => (int) $data['meses'],
                'id_tipo_precio' => (int) $data['id_tipo_precio'],
                'monto_orden' => $data['amount'],
                'monto_membresia' => (float) ($data['monto_membresia'] ?? $data['amount']),
                'recargo' => (float) ($data['recargo'] ?? 0),
                'brand' => $card->brand(),
                'last4' => $card->last4(),
                'saveCard' => $saveCard,
                'alias' => $data['alias'] ?? null,
                'holder' => $card->holder,
                'expMonth' => $card->expiryMonth,
                'expYear' => $card->expiryYear,
                // Datos del titular para ALTA_PATS (cuando no hay pasaporte aún).
                'curp' => strtoupper($pasaporte->curp ?? $data['curp_usuario'] ?? ''),
                'nombres' => $pasaporte->nombres ?? $data['nombre_usuario'] ?? '',
                'apellido_pa' => $pasaporte->apellido_pa ?? $data['apellido_pa'] ?? '',
                'apellido_ma' => $pasaporte->apellido_ma ?? $data['apellido_ma'] ?? null,
                'fecha_nacimiento' => $pasaporte->fecha_nacimiento ?? $data['fecha_nacimiento'] ?? null,
            ],
        ]);

        $threeDs = ThreeDSData::fromRequest(
            request: $request,
            shopperResultUrl: route('prosa.3ds.return', ['mtx' => $mtx]),
            email: $data['email'] ?? null,
            givenName: $card->holder,
            billing: $data['billing'] ?? null,
            browser: $data['browser'] ?? null,
        );

        try {
            $result = $this->paymentService->initiate(new ChargeData(
                card: $card,
                amount: (float) $data['amount'],
                currency: config('prosa.currency'),
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
                'status' => ProsaPendingCheckout::STATUS_CHALLENGE,
                'redirect' => $result['redirect'],
            ]);

            return response()->json([
                'success' => true,
                'status' => 'challenge',
                'challenge' => $result['redirect'],
            ]);
        }

        if ($result['status'] === 'approved') {
            $redirectUrl = $this->checkoutManager->finish($checkout, $result);

            return response()->json([
                'success' => true,
                'status' => 'approved',
                'redirectUrl' => $redirectUrl,
                'paymentId' => $result['paymentId'],
            ]);
        }

        $checkout->update([
            'status' => ProsaPendingCheckout::STATUS_DECLINED,
            'result_code' => $result['resultCode'] ?? null,
            'result_description' => $result['resultDescription'] ?? null,
        ]);

        return response()->json([
            'success' => false,
            'status' => 'declined',
            'error' => $result['resultDescription'] ?: 'El pago fue rechazado.',
            'code' => $result['resultCode'] ?? '',
        ], 400);
    }

    /**
     * POST /api/prosa/sale/recurring
     *
     * Cobro recurrente INICIAL (CIT). Crea la orden, inicia el pago con 3DS y
     * `createRegistration=true`. Al aprobarse, {@see PagosRecurringCheckout}
     * actualiza el pasaporte y guarda la suscripción para cobros automáticos.
     */
    public function recurringSale(Request $request): JsonResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'pan' => ['required', 'string'],
            'cardholderName' => ['required', 'string', 'max:128'],
            'cvv2' => ['required', 'string', 'min:3', 'max:4'],
            'expMonth' => ['required', 'string', 'max:2'],
            'expYear' => ['required', 'string', 'max:4'],
            'email' => ['nullable', 'email'],
            'browser' => ['nullable', 'array'],
            'billing' => ['nullable', 'array'],
            'billing.street1' => ['nullable', 'string', 'max:100'],
            'billing.city' => ['nullable', 'string', 'max:50'],
            'billing.state' => ['nullable', 'string', 'max:50'],
            'billing.postcode' => ['nullable', 'string', 'max:30'],
            'billing.country' => ['nullable', 'string', 'max:3'],
            'frecuencia' => ['required', 'in:MENSUAL,ANUAL'],
            'meses' => ['required', 'integer', 'min:1'],
            'id_tipo_precio' => ['required', 'integer'],
            'monto_membresia' => ['nullable', 'numeric', 'min:0'],
            'recargo' => ['nullable', 'numeric', 'min:0'],
        ]);

        $user = $request->user('pasaporte') ?? $request->user();
        $userId = $user?->getKey();

        $card = CardData::fromForm(
            number: $data['pan'],
            holder: $data['cardholderName'],
            expMonth: $data['expMonth'],
            expYear: $data['expYear'],
            cvv: $data['cvv2'],
        );

        $ahora = \Carbon\Carbon::now();
        $referencia = 'PATS-'.$ahora->format('YmdHis').'-'.strtoupper(substr(md5(uniqid()), 0, 8));
        $folio = 'ORD-'.$ahora->format('Ymd').'-'.strtoupper(substr(md5(uniqid()), 0, 6));
        $mtx = preg_replace('/[^A-Za-z0-9]/', '', $referencia);
        $correo = $user?->correo_usuario ?? ($data['email'] ?? '');

        $pasaporte = \Illuminate\Support\Facades\DB::table('pats_pasaportes')
            ->where('correo', $correo)
            ->where('activo', 1)
            ->orderBy('fecha_alta', 'desc')
            ->first();

        $operacion = $pasaporte ? 'RENOVACION_PATS' : 'ALTA_PATS';

        $idOrden = \Illuminate\Support\Facades\DB::table('pats_ordenes_pago')->insertGetId([
            'folio_orden' => $folio,
            'referencia_pago' => $referencia,
            'tipo_origen' => 'PORTAL_CLIENTE',
            'origen_checkout' => 'PORTAL_CLIENTE',
            'id_distribuidor' => $pasaporte->id_distribuidor ?? 1,
            'id_franquicia' => $pasaporte->id_franquicia ?? 1,
            'id_pasaporte' => $pasaporte->id_pasaporte ?? null,
            'correo_usuario_pats' => $correo,
            'id_tipo_precio' => $data['id_tipo_precio'],
            'tipo_operacion' => $operacion,
            'frecuencia' => $data['frecuencia'],
            'monto_orden' => $data['amount'],
            'monto_nominal_base' => $data['amount'],
            'monto_extra_recargo' => 0.00,
            'moneda' => 'MXN',
            'estatus_orden' => 'PENDIENTE',
            'estatus_pago' => 'PENDIENTE',
            'proveedor_pasarela' => 'PROSA',
            'user_creo' => $correo,
            'fecha_orden' => $ahora,
            'created_at' => $ahora,
            'updated_at' => $ahora,
        ]);

        $checkout = ProsaPendingCheckout::create([
            'merchant_transaction_id' => $mtx,
            'flow' => PagosRecurringCheckout::FLOW,
            'status' => ProsaPendingCheckout::STATUS_PENDING,
            'user_id' => $userId,
            'amount' => $data['amount'],
            'payload' => [
                'id_orden' => $idOrden,
                'referencia' => $referencia,
                'folio' => $folio,
                'operacion' => $operacion,
                'correo_usuario' => $correo,
                'frecuencia' => $data['frecuencia'],
                'meses' => (int) $data['meses'],
                'id_tipo_precio' => (int) $data['id_tipo_precio'],
                'monto_orden' => $data['amount'],
                'monto_membresia' => (float) ($data['monto_membresia'] ?? $data['amount']),
                'recargo' => (float) ($data['recargo'] ?? 0),
                'card_brand' => $card->brand(),
                'card_last4' => $card->last4(),
                'holder' => $card->holder,
                'expMonth' => $card->expiryMonth,
                'expYear' => $card->expiryYear,
                'curp' => strtoupper($pasaporte->curp ?? ''),
                'nombres' => $pasaporte->nombres ?? '',
                'apellido_pa' => $pasaporte->apellido_pa ?? '',
                'apellido_ma' => $pasaporte->apellido_ma ?? null,
                'fecha_nacimiento' => $pasaporte->fecha_nacimiento ?? null,
            ],
        ]);

        $threeDs = ThreeDSData::fromRequest(
            request: $request,
            shopperResultUrl: route('prosa.3ds.return', ['mtx' => $mtx]),
            email: $correo ?: null,
            givenName: $card->holder,
            billing: $data['billing'] ?? null,
            browser: $data['browser'] ?? null,
        );

        try {
            $result = $this->recurringService->startRecurring(
                card: $card,
                amount: (float) $data['amount'],
                currency: config('prosa.currency'),
                merchantTransactionId: $mtx,
                extraParams: $threeDs->toParams(),
            );
        } catch (ProsaTimeoutException $e) {
            $checkout->update(['status' => ProsaPendingCheckout::STATUS_DECLINED]);
            $this->logTimeout('recurring', $data['amount']);

            return $this->timeoutResponse();
        }

        return $this->respondToInitiation($checkout, $result);
    }

    /**
     * POST /api/prosa/sale/oxxo
     *
     * Genera una ficha de pago OXXO en OPPWA. Devuelve la referencia numérica
     * y la URL de voucher para mostrar al usuario. El pago se confirma de forma
     * asíncrona vía webhook cuando el cliente paga en tienda.
     */
    public function oxxoSale(Request $request): JsonResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'email' => ['required', 'email'],
            'name' => ['required', 'string', 'max:128'],
            'frecuencia' => ['required', 'in:MENSUAL,ANUAL'],
            'meses' => ['required', 'integer', 'min:1'],
            'id_tipo_precio' => ['required', 'integer'],
            'monto_membresia' => ['nullable', 'numeric', 'min:0'],
            'recargo' => ['nullable', 'numeric', 'min:0'],
        ]);

        $user = $request->user('pasaporte') ?? $request->user();
        $userId = $user?->getKey();
        $correo = $user?->correo_usuario ?? $data['email'];
        $mtx = $this->reference();

        // Separar nombre en givenName / surname para OPPWA.
        $parts = explode(' ', trim($data['name']), 2);
        $givenName = $parts[0];
        $surname = $parts[1] ?? $parts[0];

        $pasaporte = \Illuminate\Support\Facades\DB::table('pats_pasaportes')
            ->where('correo', $correo)
            ->where('activo', 1)
            ->orderBy('fecha_alta', 'desc')
            ->first();

        $operacion = $pasaporte ? 'RENOVACION_PATS' : 'ALTA_PATS';

        $ahora = \Carbon\Carbon::now();
        $referencia = 'PATS-'.$ahora->format('YmdHis').'-'.strtoupper(substr(md5(uniqid()), 0, 8));
        $folio = 'ORD-'.$ahora->format('Ymd').'-'.strtoupper(substr(md5(uniqid()), 0, 6));

        $idOrden = \Illuminate\Support\Facades\DB::table('pats_ordenes_pago')->insertGetId([
            'folio_orden' => $folio,
            'referencia_pago' => $referencia,
            'tipo_origen' => 'PORTAL_CLIENTE',
            'origen_checkout' => 'PORTAL_CLIENTE',
            'id_distribuidor' => $pasaporte->id_distribuidor ?? 1,
            'id_franquicia' => $pasaporte->id_franquicia ?? 1,
            'id_pasaporte' => $pasaporte->id_pasaporte ?? null,
            'correo_usuario_pats' => $correo,
            'id_tipo_precio' => $data['id_tipo_precio'],
            'tipo_operacion' => $operacion,
            'frecuencia' => $data['frecuencia'],
            'monto_orden' => $data['amount'],
            'monto_nominal_base' => $data['amount'],
            'monto_extra_recargo' => 0.00,
            'moneda' => 'MXN',
            'estatus_orden' => 'PENDIENTE',
            'estatus_pago' => 'PENDIENTE',
            'proveedor_pasarela' => 'PROSA',
            'user_creo' => $correo,
            'fecha_orden' => $ahora,
            'created_at' => $ahora,
            'updated_at' => $ahora,
        ]);

        // Crear el ProsaPendingCheckout con el contexto del plan.
        // El webhook usará este checkout para completar la renovación.
        $checkout = ProsaPendingCheckout::create([
            'merchant_transaction_id' => $mtx,
            'flow' => PagosCheckout::FLOW,
            'status' => ProsaPendingCheckout::STATUS_PENDING,
            'user_id' => $userId,
            'amount' => $data['amount'],
            'payload' => [
                'origen' => 'oxxo',
                'id_orden' => $idOrden,
                'referencia' => $referencia,
                'folio' => $folio,
                'operacion' => $operacion,
                'correo_usuario' => $correo,
                'frecuencia' => $data['frecuencia'],
                'meses' => (int) $data['meses'],
                'id_tipo_precio' => (int) $data['id_tipo_precio'],
                'monto_orden' => $data['amount'],
                'monto_membresia' => (float) ($data['monto_membresia'] ?? $data['amount']),
                'recargo' => (float) ($data['recargo'] ?? 0),
                'curp' => strtoupper($pasaporte->curp ?? ''),
                'nombres' => $pasaporte->nombres ?? $givenName,
                'apellido_pa' => $pasaporte->apellido_pa ?? $surname,
                'apellido_ma' => $pasaporte->apellido_ma ?? null,
                'fecha_nacimiento' => $pasaporte->fecha_nacimiento ?? null,
                'email' => $data['email'],
                'name' => $data['name'],
            ],
        ]);

        try {
            $result = $this->oxxoService->createVoucher([
                'amount' => (float) $data['amount'],
                'email' => $data['email'],
                'givenName' => $givenName,
                'surname' => $surname,
                'ip' => $request->ip(),
                'merchantTransactionId' => $mtx,
            ]);
        } catch (ProsaTimeoutException) {
            $checkout->update(['status' => ProsaPendingCheckout::STATUS_DECLINED]);

            return $this->timeoutResponse();
        } catch (ProsaException $e) {
            $checkout->update(['status' => ProsaPendingCheckout::STATUS_DECLINED]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'code' => $e->resultCode,
            ], 400);
        }

        $checkout->update(['payment_id' => $result['paymentId']]);

        ProsaTransaction::create([
            'user_id' => $userId,
            'payment_id' => $result['paymentId'],
            'payment_type' => 'PA',
            'amount' => $data['amount'],
            'currency' => config('prosa.currency'),
            'result_code' => $result['resultCode'],
            'brand' => 'OXXO',
            'status' => 'pending',
            'origen' => 'oxxo',
            'raw_response' => $result['raw'],
        ]);

        return response()->json([
            'success' => true,
            'paymentId' => $result['paymentId'],
            'reference' => $result['reference'],
            'redirectUrl' => $result['redirectUrl'],
            'amount' => $data['amount'],
        ]);
    }

    /**
     * POST /api/prosa/refund
     */
    public function refund(Request $request): JsonResponse
    {
        $data = $request->validate([
            'paymentId' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:0.01'],
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
            'user_id' => $request->user()?->id,
            'payment_id' => $result['paymentId'],
            'payment_type' => 'RF',
            'amount' => $data['amount'],
            'currency' => config('prosa.currency'),
            'result_code' => $result['resultCode'],
            'result_description' => $result['resultDescription'],
            'status' => 'refunded',
            'origen' => 'refund',
            'raw_response' => $result['raw'],
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
            'user_id' => $request->user()?->id,
            'payment_id' => $result['paymentId'],
            'payment_type' => 'RV',
            'currency' => config('prosa.currency'),
            'result_code' => $result['resultCode'],
            'result_description' => $result['resultDescription'],
            'status' => 'reversed',
            'origen' => 'reversal',
            'raw_response' => $result['raw'],
        ]);

        return response()->json(['success' => true, 'code' => $result['resultCode']]);
    }

    // ── Helpers ───────────────────────────────────────────────

    private function reference(): string
    {
        return 'PATS-'.now()->format('YmdHis').'-'.strtoupper(substr(md5(uniqid()), 0, 8));
    }

    /**
     * @param  array<string, mixed>  $result
     */
    private function logTransaction(string $origen, array $result, CardData $card): ProsaTransaction
    {
        return ProsaTransaction::create([
            'user_id' => request()->user('pasaporte')?->getKey() ?? request()->user()?->getKey(),
            'registration_id' => $result['registrationId'] ?? null,
            'payment_id' => $result['paymentId'],
            'payment_type' => 'DB',
            'amount' => $result['amount'],
            'currency' => $result['currency'] ?: config('prosa.currency'),
            'result_code' => $result['resultCode'],
            'result_description' => $result['resultDescription'],
            'brand' => $result['brand'] ?: $card->brand(),
            'last4' => $result['last4'] ?? $card->last4(),
            'status' => 'approved',
            'origen' => $origen,
            'raw_response' => $result['raw'],
        ]);
    }

    private function logTimeout(string $origen, mixed $amount): void
    {
        ProsaTransaction::create([
            'user_id' => request()->user('pasaporte')?->getKey() ?? request()->user()?->getKey(),
            'payment_type' => 'DB',
            'amount' => $amount,
            'currency' => config('prosa.currency'),
            'status' => 'timeout',
            'origen' => $origen,
        ]);
    }

    private function timeoutResponse(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => 'Timeout al procesar la transacción. Verifica el estado antes de reintentar.',
            'code' => 'TIMEOUT',
        ], 504);
    }

    private function errorResponse(ProsaException $e): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'code' => $e->resultCode,
        ], 400);
    }
}
