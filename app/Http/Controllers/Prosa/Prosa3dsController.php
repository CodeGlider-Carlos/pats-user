<?php

namespace App\Http\Controllers\Prosa;

use App\Http\Controllers\Controller;
use App\Models\ProsaPendingCheckout;
use App\Services\Prosa\Checkout\CheckoutManager;
use App\Services\Prosa\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Recibe el retorno del cliente tras el reto 3-D Secure (shopperResultUrl).
 *
 * OPPWA redirige el navegador aquí con un `resourcePath` (o el id del pago);
 * se consulta el resultado final y se finaliza el flujo de negocio mediante
 * el {@see CheckoutManager}.
 */
class Prosa3dsController extends Controller
{
    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly CheckoutManager $manager,
    ) {}

    /**
     * GET|POST /prosa/3ds/return/{mtx}
     */
    public function return(Request $request, string $mtx)
    {
        $checkout = ProsaPendingCheckout::where('merchant_transaction_id', $mtx)->first();

        if (! $checkout) {
            abort(404, 'Checkout no encontrado.');
        }

        $resourcePath = $request->input('resourcePath');

        try {
            $result = $resourcePath
                ? $this->paymentService->statusByResourcePath($resourcePath)
                : $this->paymentService->status((string) $checkout->payment_id);
        } catch (\Throwable $e) {
            Log::channel('prosa')->error('Prosa 3DS return status error', [
                'mtx'   => $mtx,
                'error' => $e->getMessage(),
            ]);
            $result = ['status' => 'declined', 'approved' => false, 'resultDescription' => 'No fue posible verificar el pago.'];
        }

        $url = $this->manager->finish($checkout, $result);

        // Para flujos de solicitud el completer devuelve una URL relativa (/ruta?params)
        // o una URL completa. Normalizar antes de redirigir.
        if (str_starts_with($url, '/') || str_starts_with($url, 'http')) {
            return redirect()->away($url);
        }

        return redirect($url);
    }
}
