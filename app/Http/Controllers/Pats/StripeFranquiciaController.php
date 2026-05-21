<?php

namespace App\Http\Controllers\Pats;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\{DB, Log};
use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripeFranquiciaController extends Controller
{
    private const PRECIO_DEFAULT  = 1000000.00;
    private const STRIPE_MAX_MXN  = 99999900; // $999,999 MXN en centavos (límite de Stripe)

    /**
     * POST /franquicia/stripe/intent
     *
     * Si el monto supera el límite de Stripe, devuelve dos client_secrets
     * (split: true). El JS confirma ambos de forma secuencial.
     */
    public function createIntent(Request $request): JsonResponse
    {
        $precio         = $this->getPrecioFranquicia();
        $amountCentavos = (int) round($precio * 100);
        $modalidad      = strtoupper(trim((string) $request->input('modalidad_pago', 'CONTADO')));
        $plazoMeses     = (int) $request->input('plazo_meses', 0);
        $intentKey      = substr(trim((string) $request->input('intent_key', '')), 0, 64);

        Stripe::setApiKey(config('services.stripe.secret'));

        $meta = [
            'correo'      => substr((string) $request->input('correo', ''), 0, 100),
            'nombre'      => substr((string) $request->input('nombre', ''), 0, 100),
            'origen'      => 'solicitud_franquicia',
            'modalidad'   => $modalidad,
            'plazo_meses' => (string) $plazoMeses,
        ];

        $baseParams = [
            'currency' => 'mxn',
            'metadata' => $meta,
            'automatic_payment_methods' => [
                'enabled'         => true,
                'allow_redirects' => 'never',
            ],
        ];

        if ($modalidad === 'DIFERIDO' && $plazoMeses > 0) {
            $baseParams['payment_method_options'] = [
                'card' => ['installments' => ['enabled' => true]],
            ];
        }

        try {
            if ($amountCentavos > self::STRIPE_MAX_MXN) {
                // ── Cobro dividido en dos intents ──────────────────────────────
                $amount1 = self::STRIPE_MAX_MXN;
                $amount2 = $amountCentavos - $amount1;

                $opts1 = $intentKey !== '' ? ['idempotency_key' => 'franq_a_' . $intentKey] : [];
                $opts2 = $intentKey !== '' ? ['idempotency_key' => 'franq_b_' . $intentKey] : [];

                $intent1 = PaymentIntent::create(array_merge($baseParams, ['amount' => $amount1]), $opts1);
                $intent2 = PaymentIntent::create(array_merge($baseParams, ['amount' => $amount2]), $opts2);

                return response()->json([
                    'ok'              => true,
                    'split'           => true,
                    'client_secret_1' => $intent1->client_secret,
                    'client_secret_2' => $intent2->client_secret,
                    'amount'          => $precio,
                    'modalidad'       => $modalidad,
                    'plazo_meses'     => $plazoMeses,
                ]);
            }

            // ── Cobro normal (monto dentro del límite) ─────────────────────────
            $opts = $intentKey !== '' ? ['idempotency_key' => 'franq_' . $intentKey] : [];
            $intent = PaymentIntent::create(array_merge($baseParams, ['amount' => $amountCentavos]), $opts);

            return response()->json([
                'ok'            => true,
                'split'         => false,
                'client_secret' => $intent->client_secret,
                'amount'        => $precio,
                'modalidad'     => $modalidad,
                'plazo_meses'   => $plazoMeses,
            ]);

        } catch (\Throwable $e) {
            Log::error('Stripe.createIntent (franquicia)', ['error' => $e->getMessage()]);
            return response()->json(['ok' => false, 'error' => 'No fue posible iniciar el proceso de pago.'], 500);
        }
    }

    private function getPrecioFranquicia(): float
    {
        $precio = DB::table('pats_cat_precios')
            ->whereRaw("LOWER(TRIM(tipo)) = 'franquicia'")
            ->where('activo', 1)
            ->orderBy('id')
            ->value('precio');

        return (float) ($precio ?? self::PRECIO_DEFAULT);
    }
}
