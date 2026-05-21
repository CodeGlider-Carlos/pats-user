<?php

namespace App\Events\Feenicia;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Se dispara cuando Feenicia envía una notificación por webhook.
 * Tipos: SALE, CANCEL, REVERSAL, REFUND
 *
 * El Job ProcessFeeniciaNotification escucha este evento
 * y lo procesa de forma asíncrona.
 */
class FeeniciaNotificationReceived
{
    use Dispatchable, SerializesModels;

    /**
     * @param array<string, mixed> $payload  El JSON completo recibido del webhook
     * @param string               $tipoTx   SALE | CANCEL | REVERSAL | REFUND
     * @param string               $merchant MerchantId de 16 dígitos
     */
    public function __construct(
        public readonly array  $payload,
        public readonly string $tipoTx,
        public readonly string $merchant,
    ) {}
}
