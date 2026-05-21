<?php

namespace App\Http\Controllers\Pats;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\{DB, Log};
use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripeDistribucionController extends Controller
{
    private const PRECIO_DEFAULT = 20000.00;

    /**
     * Crea un PaymentIntent de Stripe con el monto real de distribución.
     * Devuelve el client_secret para que el frontend confirme el pago.
     *
     * POST /distribucion/stripe/intent
     * POST /pats/distribucion/stripe/intent
     */
    public function createIntent(Request $request): JsonResponse
    {
        $precio         = $this->getPrecioDistribucion();
        $amountCentavos = (int) round($precio * 100);
        $modalidad      = strtoupper(trim((string) $request->input('modalidad_pago', 'CONTADO')));
        $plazoMeses     = (int) $request->input('plazo_meses', 0);
        $intentKey      = substr(trim((string) $request->input('intent_key', '')), 0, 64);

        Stripe::setApiKey(config('services.stripe.secret'));

        $intentParams = [
            'amount'   => $amountCentavos,
            'currency' => 'mxn',
            'metadata' => [
                'correo'     => substr((string) $request->input('correo', ''), 0, 100),
                'nombre'     => substr((string) $request->input('nombre', ''), 0, 100),
                'origen'     => 'solicitud_distribuidor',
                'modalidad'  => $modalidad,
                'plazo_meses'=> (string) $plazoMeses,
            ],
            'automatic_payment_methods' => [
                'enabled'         => true,
                'allow_redirects' => 'never',
            ],
        ];

        // Habilitar meses sin intereses solo para pago diferido con plazo válido
        if ($modalidad === 'DIFERIDO' && $plazoMeses > 0) {
            $intentParams['payment_method_options'] = [
                'card' => [
                    'installments' => ['enabled' => true],
                ],
            ];
        }

        $options = [];
        if ($intentKey !== '') {
            $options['idempotency_key'] = 'dist_' . $intentKey;
        }

        try {
            $intent = PaymentIntent::create($intentParams, $options);
        } catch (\Throwable $e) {
            Log::error('Stripe.createIntent', ['error' => $e->getMessage()]);
            return response()->json(['ok' => false, 'error' => 'No fue posible iniciar el proceso de pago.'], 500);
        }

        return response()->json([
            'ok'            => true,
            'client_secret' => $intent->client_secret,
            'amount'        => $precio,
            'modalidad'     => $modalidad,
            'plazo_meses'   => $plazoMeses,
        ]);
    }

    private function getPrecioDistribucion(): float
    {
        $precio = DB::table('pats_cat_precios')
            ->whereRaw("LOWER(TRIM(tipo)) = 'distribucion'")
            ->where('activo', 1)
            ->orderBy('id')
            ->value('precio');

        return (float) ($precio ?? self::PRECIO_DEFAULT);
    }
}