<?php

namespace App\Jobs\Feenicia;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\FeeniciaTransaction;
use App\Models\FeeniciaWebhookLog;

/**
 * Procesa una notificación de Feenicia de forma asíncrona.
 *
 * Al procesar en un Job:
 *  - El webhook responde 200 inmediatamente (Feenicia no reintenta)
 *  - El procesamiento pesado ocurre en segundo plano
 *  - Si el Job falla, Laravel lo reintenta automáticamente
 *
 * Para activar las queues en Laravel:
 *   php artisan queue:work
 */
class ProcessFeeniciaNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 10; // segundos entre reintentos

    public function __construct(
        private readonly array  $payload,
        private readonly string $tipoTx,
        private readonly int    $webhookLogId,
    ) {}

    public function handle(): void
    {
        Log::channel('feenicia')->info("Webhook procesando {$this->tipoTx}", [
            'id'     => $this->payload['id'] ?? null,
            'import' => $this->payload['importe'] ?? null,
        ]);

        match ($this->tipoTx) {
            'SALE'     => $this->handleSale(),
            'CANCEL'   => $this->handlePostSale(FeeniciaTransaction::STATUS_CANCELLED),
            'REVERSAL' => $this->handlePostSale(FeeniciaTransaction::STATUS_REVERSED),
            'REFUND'   => $this->handlePostSale(FeeniciaTransaction::STATUS_REFUNDED),
            default    => Log::channel('feenicia')->warning("Webhook: tipoTx desconocido: {$this->tipoTx}"),
        };

        // Marcar el log como procesado
        FeeniciaWebhookLog::find($this->webhookLogId)
            ?->update(['processed' => true]);
    }

    // ──────────────────────────────────────────────
    //  Handlers por tipo
    // ──────────────────────────────────────────────

    private function handleSale(): void
    {
        $p = $this->payload;

        // Buscar si ya tenemos la tx por claveVenta o transactionId
        $exists = FeeniciaTransaction::where('transaction_id', $p['id'] ?? null)
                                     ->orWhere('order_id', $p['claveVenta'] ?? null)
                                     ->exists();

        if ($exists) {
            Log::channel('feenicia')->info('Webhook SALE: transacción ya registrada, omitiendo');
            return;
        }

        // Si la transacción no existe en nuestra BD (venta desde otro canal),
        // la registramos desde el webhook
        FeeniciaTransaction::create([
            'type'                 => FeeniciaTransaction::TYPE_ONE_STEP_SALE,
            'status'               => $p['aprobada'] ? FeeniciaTransaction::STATUS_APPROVED
                                                     : FeeniciaTransaction::STATUS_REJECTED,
            'transaction_id'       => (string) ($p['id'] ?? ''),
            'order_id'             => $p['claveVenta'] ?? null,
            'authnum'              => $p['autorizacion'] ?? null,
            'affiliation'          => $p['afiliacion'] ?? '',
            'merchant'             => $p['merchant'] ?? config('feenicia.merchant'),
            'amount'               => $p['importe'] ?? 0,
            'tip'                  => $p['propina'] ?? 0,
            'approved'             => $p['aprobada'] ?? false,
            'card_brand'           => $p['marca'] ?? null,
            'card_last4'           => $p['panTerminacion'] ?? null,
            'card_first6'          => $p['bin'] ?? null,
            'issuer_bank'          => $p['bancoEmisor'] ?? null,
            'acquirer_bank'        => $p['bancoAdquiriente'] ?? null,
            'msi_payments'         => $p['msi']['numeroPagos'] ?? null,
            'msi_plan_type'        => $p['msi']['tipoPlan'] ?? null,
            'feenicia_response'    => $p,
        ]);
    }

    private function handlePostSale(string $newStatus): void
    {
        $p = $this->payload;

        // Buscar la transacción original y actualizar su estado
        $tx = FeeniciaTransaction::where('transaction_id', (string) ($p['id'] ?? ''))
                                 ->orWhere('order_id', $p['claveVenta'] ?? null)
                                 ->first();

        if ($tx) {
            $tx->update(['status' => $newStatus]);
            Log::channel('feenicia')->info("Webhook {$this->tipoTx}: tx {$tx->id} → {$newStatus}");
        } else {
            Log::channel('feenicia')->warning("Webhook {$this->tipoTx}: transacción no encontrada", [
                'id'         => $p['id'] ?? null,
                'claveVenta' => $p['claveVenta'] ?? null,
            ]);
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::channel('feenicia')->error("Webhook Job falló definitivamente", [
            'tipoTx'  => $this->tipoTx,
            'error'   => $e->getMessage(),
            'payload' => $this->payload['id'] ?? null,
        ]);
    }
}
