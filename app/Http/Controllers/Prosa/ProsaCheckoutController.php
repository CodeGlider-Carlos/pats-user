<?php

namespace App\Http\Controllers\Prosa;

use App\DTO\Prosa\CardData;
use App\DTO\Prosa\ChargeData;
use App\DTO\Prosa\ThreeDSData;
use App\Exceptions\Prosa\ProsaTimeoutException;
use App\Http\Controllers\Controller;
use App\Models\ProsaPendingCheckout;
use App\Models\ProsaTransaction;
use App\Services\Prosa\Checkout\CheckoutManager;
use App\Services\Prosa\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Cobro server-to-server para los flujos de alta B2B (solicitudes).
 *
 * Reemplaza a los antiguos Stripe*Controller::createIntent. En vez de devolver
 * un client_secret, ejecuta el cargo directamente y regresa el `payment_id`
 * que el submit de la solicitud usa como referencia de pasarela.
 *
 * Nota: los Meses Sin Intereses (modalidad DIFERIDO) de Stripe quedan fuera de
 * alcance; el monto se cobra en una sola exhibición.
 */
class ProsaCheckoutController extends Controller
{
    private const PRECIO_PATS_MENSUAL = 800.00;

    private const PRECIO_PATS_ANUAL = 9600.00;

    private const PRECIO_DISTRIBUCION = 20000.00;

    private const PRECIO_FRANQUICIA = 999999.00;

    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly CheckoutManager $checkoutManager,
    ) {}

    /**
     * POST /pats/registro/prosa/charge
     */
    public function patsRegistro(Request $request): JsonResponse
    {
        $frecuencia = strtoupper(trim((string) $request->input('frecuencia', 'MENSUAL')));
        if (! in_array($frecuencia, ['MENSUAL', 'ANUAL'], true)) {
            $frecuencia = 'MENSUAL';
        }

        $precio = $this->precioPats($frecuencia);

        return $this->charge($request, $precio, 'solicitud_pats', 'solicitud_pats', [
            'form_url' => url('/pats/registro'),
        ]);
    }

    /**
     * POST /distribucion/prosa/charge
     */
    public function distribucion(Request $request): JsonResponse
    {
        $precio = $this->precioCatalogo('distribucion', self::PRECIO_DISTRIBUCION);

        return $this->charge($request, $precio, 'solicitud_distribuidor', 'solicitud_distribuidor', [
            'form_url' => url('/distribucion/solicitud'),
        ]);
    }

    /**
     * POST /franquicia/prosa/charge
     */
    public function franquicia(Request $request): JsonResponse
    {
        $precio = $this->precioCatalogo('franquicia', self::PRECIO_FRANQUICIA);

        return $this->charge($request, $precio, 'solicitud_franquicia', 'solicitud_franquicia', [
            'form_url' => url('/franquicia/solicitud'),
        ]);
    }

    /**
     * POST /distribucion/link/{token}/prosa/charge
     */
    public function distribucionLink(Request $request, string $token): JsonResponse
    {
        if (! session("dist_link_auth_{$token}")) {
            return response()->json(['ok' => false, 'error' => 'Sesión no autorizada.'], 401);
        }

        $link = DB::table('distribuidor_links')
            ->where('token', $token)
            ->where('active', 1)
            ->where('id_solicitud', 0)
            ->first();

        if ($link === null) {
            return response()->json(['ok' => false, 'error' => 'El enlace no está disponible.'], 404);
        }

        if ($link->type_pay !== 'card') {
            return response()->json(['ok' => false, 'error' => 'Este enlace no requiere pago con tarjeta.'], 400);
        }

        return $this->charge($request, (float) $link->amount, 'distribuidor_link', 'solicitud_distribuidor_link', [
            'form_url' => url('/distribucion/link/'.$token.'/formulario'),
        ]);
    }

    // ── Núcleo ────────────────────────────────────────────────

    /**
     * Inicia un cobro 3DS para los flujos de solicitud.
     *
     * Devuelve uno de tres estados:
     *  - { ok: true,  status: 'approved',  payment_id }  → frictionless, pago confirmado.
     *  - { ok: true,  status: 'challenge', challenge }    → redirigir al ACS.
     *  - { ok: false, status: 'declined',  error, code }  → rechazado.
     *
     * @param  string  $flowName  Identificador del completer ('solicitud_franquicia', etc.)
     * @param  array<string, mixed>  $extra  Datos adicionales que el completer necesita (id_solicitud, urls, etc.)
     */
    private function charge(
        Request $request,
        float $amount,
        string $origen,
        string $flowName,
        array $extra = [],
    ): JsonResponse {
        $data = $request->validate([
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
        ]);

        $card = CardData::fromForm(
            number: $data['pan'],
            holder: $data['cardholderName'],
            expMonth: $data['expMonth'],
            expYear: $data['expYear'],
            cvv: $data['cvv2'],
        );

        $mtx = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($origen).now()->format('YmdHis').strtoupper(bin2hex(random_bytes(3))));

        $checkout = ProsaPendingCheckout::create([
            'merchant_transaction_id' => $mtx,
            'flow' => $flowName,
            'status' => ProsaPendingCheckout::STATUS_PENDING,
            'user_id' => $request->user()?->id,
            'amount' => $amount,
            'payload' => array_merge($extra, [
                'origen' => $origen,
                'brand' => $card->brand(),
                'last4' => $card->last4(),
            ]),
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
                amount: $amount,
                currency: config('prosa.currency'),
                merchantTransactionId: $mtx,
            ), $threeDs);
        } catch (ProsaTimeoutException $e) {
            $checkout->update(['status' => ProsaPendingCheckout::STATUS_DECLINED]);

            return response()->json([
                'ok' => false,
                'error' => 'Timeout al procesar el pago. Verifica antes de reintentar.',
                'code' => 'TIMEOUT',
            ], 504);
        }

        $checkout->update(['payment_id' => $result['paymentId'] ?? null]);

        if ($result['status'] === 'challenge') {
            $checkout->update([
                'status' => ProsaPendingCheckout::STATUS_CHALLENGE,
                'redirect' => $result['redirect'],
            ]);

            return response()->json([
                'ok' => true,
                'status' => 'challenge',
                'challenge' => $result['redirect'],
            ]);
        }

        if ($result['status'] === 'approved') {
            ProsaTransaction::create([
                'user_id' => $request->user()?->id,
                'payment_id' => $result['paymentId'],
                'payment_type' => 'DB',
                'amount' => $amount,
                'currency' => config('prosa.currency'),
                'result_code' => $result['resultCode'],
                'result_description' => $result['resultDescription'],
                'brand' => $result['brand'] ?: $card->brand(),
                'last4' => $result['last4'] ?? $card->last4(),
                'status' => 'approved',
                'origen' => $origen,
                'raw_response' => $result['raw'],
            ]);

            $this->checkoutManager->finish($checkout, $result);

            return response()->json([
                'ok' => true,
                'status' => 'approved',
                'payment_id' => $result['paymentId'],
                'amount' => $amount,
            ]);
        }

        $checkout->update([
            'status' => ProsaPendingCheckout::STATUS_DECLINED,
            'result_code' => $result['resultCode'] ?? null,
            'result_description' => $result['resultDescription'] ?? null,
        ]);

        return response()->json([
            'ok' => false,
            'status' => 'declined',
            'error' => $result['resultDescription'] ?: 'El pago fue rechazado.',
            'code' => $result['resultCode'] ?? '',
        ], 400);
    }

    private function precioPats(string $frecuencia): float
    {
        $tipo = $frecuencia === 'ANUAL' ? 'pats_anual' : 'pats_mensual';
        $fallback = $frecuencia === 'ANUAL' ? self::PRECIO_PATS_ANUAL : self::PRECIO_PATS_MENSUAL;

        return $this->precioCatalogo($tipo, $fallback);
    }

    private function precioCatalogo(string $tipo, float $fallback): float
    {
        $precio = DB::table('pats_cat_precios')
            ->whereRaw('LOWER(TRIM(tipo)) = ?', [$tipo])
            ->where('activo', 1)
            ->orderBy('id')
            ->value('precio');

        return (float) ($precio ?? $fallback);
    }
}
