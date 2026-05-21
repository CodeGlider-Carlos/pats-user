<?php

namespace App\Http\Controllers\Feenicia;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Jobs\Feenicia\ProcessFeeniciaNotification;
use App\Models\FeeniciaWebhookLog;
use Illuminate\Support\Facades\Log;

/**
 * Recibe las notificaciones push de Feenicia (Webhook / Atena).
 *
 * Tipos de notificación: SALE, CANCEL, REVERSAL, REFUND
 *
 * Flujo:
 *  1. El middleware ValidateWebhookJwt valida la firma JWT antes de llegar aquí
 *  2. Se loggea el payload completo en feenicia_webhook_logs
 *  3. Se despacha el Job ProcessFeeniciaNotification de forma asíncrona
 *  4. Se responde 200 inmediatamente para que Feenicia no reintente
 *
 * IMPORTANTE: La ruta de este controller debe estar excluida del
 * middleware VerifyCsrfToken. Agregar en app/Http/Middleware/VerifyCsrfToken.php:
 *
 *   protected $except = [
 *       'api/feenicia/webhook',
 *   ];
 */
class FeeniciaWebhookController extends Controller
{
    /**
     * POST /api/feenicia/webhook
     */
    public function receive(Request $request): JsonResponse
    {
        $payload = $request->json()->all();
        $tipoTx  = $payload['tipoTx'] ?? 'UNKNOWN';
        $merchant = $payload['merchant'] ?? '';

        Log::channel('feenicia')->info("Webhook recibido: {$tipoTx}", [
            'feenicia_id' => $payload['id'] ?? null,
            'merchant'    => $merchant,
            'importe'     => $payload['importe'] ?? null,
        ]);

        // ── Guardar log del webhook ──
        $log = FeeniciaWebhookLog::create([
            'tipo_tx'     => $tipoTx,
            'merchant'    => $merchant,
            'feenicia_id' => $payload['id'] ?? 0,
            'payload'     => $payload,
            'jwt_valid'   => true, // llegó hasta aquí = JWT válido (middleware lo verificó)
            'processed'   => false,
            'ip'          => $request->ip(),
        ]);

        // ── Despachar Job asíncrono ──
        // El webhook responde 200 de inmediato y el Job procesa en background
        ProcessFeeniciaNotification::dispatch(
            payload:      $payload,
            tipoTx:       $tipoTx,
            webhookLogId: $log->id,
        );

        // Feenicia espera cualquier respuesta 2xx para confirmar recepción
        return response()->json(['received' => true]);
    }
}
