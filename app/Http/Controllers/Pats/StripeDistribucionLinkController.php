<?php

namespace App\Http\Controllers\Pats;

use App\Http\Controllers\Controller;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\{DB, Log};
use Stripe\PaymentIntent;
use Stripe\Stripe;

class StripeDistribucionLinkController extends Controller
{
    /**
     * POST /distribucion/link/{token}/stripe/intent
     * Crea un PaymentIntent con el monto definido en el link.
     */
    public function createIntent(Request $request, string $token): JsonResponse
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

        $amountCentavos = (int) round((float) $link->amount * 100);
        $modalidad      = strtoupper(trim((string) $request->input('modalidad_pago', 'CONTADO')));
        $plazoMeses     = (int) $request->input('plazo_meses', 0);
        $intentKey      = substr(trim((string) $request->input('intent_key', '')), 0, 64);

        Stripe::setApiKey(config('services.stripe.secret'));

        $intentParams = [
            'amount'   => $amountCentavos,
            'currency' => 'mxn',
            'metadata' => [
                'correo'      => substr((string) $request->input('correo', ''), 0, 100),
                'nombre'      => substr((string) $request->input('nombre', ''), 0, 100),
                'origen'      => 'distribuidor_link',
                'link_token'  => $token,
                'modalidad'   => $modalidad,
                'plazo_meses' => (string) $plazoMeses,
            ],
            'automatic_payment_methods' => ['enabled' => true, 'allow_redirects' => 'never'],
        ];

        if ($modalidad === 'DIFERIDO' && $plazoMeses > 0) {
            $intentParams['payment_method_options'] = [
                'card' => ['installments' => ['enabled' => true]],
            ];
        }

        $options = [];
        if ($intentKey !== '') {
            $options['idempotency_key'] = 'link_' . $token . '_' . $intentKey;
        }

        try {
            $intent = PaymentIntent::create($intentParams, $options);
        } catch (\Throwable $e) {
            Log::error('StripeDistribucionLink.createIntent', ['error' => $e->getMessage()]);
            return response()->json(['ok' => false, 'error' => 'No fue posible iniciar el pago.'], 500);
        }

        return response()->json([
            'ok'            => true,
            'client_secret' => $intent->client_secret,
            'amount'        => (float) $link->amount,
            'modalidad'     => $modalidad,
            'plazo_meses'   => $plazoMeses,
        ]);
    }
}
