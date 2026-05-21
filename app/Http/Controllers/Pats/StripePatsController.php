<?php

namespace App\Http\Controllers\Pats;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\{DB, Log};
use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripePatsController extends Controller
{
    private const PRECIO_MENSUAL = 800.00;
    private const PRECIO_ANUAL   = 9600.00;

    /**
     * Crea un PaymentIntent de Stripe para membresía PATS.
     * Devuelve client_secret para que el frontend confirme el pago.
     *
     * POST /pats/registro/stripe/intent
     */
    public function createIntent(Request $request): JsonResponse
    {
        $frecuencia = strtoupper(trim((string) $request->input('frecuencia', 'MENSUAL')));
        if (!in_array($frecuencia, ['MENSUAL', 'ANUAL'], true)) {
            $frecuencia = 'MENSUAL';
        }

        $precio         = $this->getPrecio($frecuencia);
        $amountCentavos = (int) round($precio * 100);
        $intentKey      = substr(trim((string) $request->input('intent_key', '')), 0, 64);

        Stripe::setApiKey(config('services.stripe.secret'));

        $intentParams = [
            'amount'   => $amountCentavos,
            'currency' => 'mxn',
            'metadata' => [
                'correo'     => substr((string) $request->input('correo', ''), 0, 100),
                'nombre'     => substr((string) $request->input('nombre', ''), 0, 100),
                'origen'     => 'solicitud_pats',
                'frecuencia' => $frecuencia,
            ],
            'automatic_payment_methods' => [
                'enabled'         => true,
                'allow_redirects' => 'never',
            ],
        ];

        $options = [];
        if ($intentKey !== '') {
            $options['idempotency_key'] = 'pats_' . $intentKey;
        }

        try {
            $intent = PaymentIntent::create($intentParams, $options);
        } catch (\Throwable $e) {
            Log::error('StripePats.createIntent', ['error' => $e->getMessage()]);
            return response()->json(['ok' => false, 'error' => 'No fue posible iniciar el proceso de pago.'], 500);
        }

        return response()->json([
            'ok'            => true,
            'client_secret' => $intent->client_secret,
            'amount'        => $precio,
            'frecuencia'    => $frecuencia,
        ]);
    }

    private function getPrecio(string $frecuencia): float
    {
        $tipo = $frecuencia === 'ANUAL' ? 'pats_anual' : 'pats_mensual';

        $precio = DB::table('pats_cat_precios')
            ->whereRaw("LOWER(TRIM(tipo)) = ?", [$tipo])
            ->where('activo', 1)
            ->orderBy('id')
            ->value('precio');

        if ($precio === null) {
            return $frecuencia === 'ANUAL' ? self::PRECIO_ANUAL : self::PRECIO_MENSUAL;
        }

        return (float) $precio;
    }
}
