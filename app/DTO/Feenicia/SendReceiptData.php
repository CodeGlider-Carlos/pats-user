<?php

namespace App\DTO\Feenicia;

/**
 * Paso e) — Enviar recibo por email (opcional).
 * Compartido por: Cash Sale, Recurring Billing, Manual Sale (5 pasos).
 * Endpoint: POST /receipt/receipt/SendReceipt
 *
 * Nota: El email debe ir encriptado.
 */
class SendReceiptData extends FeeniciaBaseData
{
    /**
     * @param string   $receiptId  Del paso d)
     * @param string[] $Email      Array con el email encriptado del tarjetahabiente
     */
    public function __construct(
        public readonly string $receiptId,
        public readonly array  $Email,
    ) {}

    public static function encryptedFields(): array
    {
        return ['Email'];
    }
}
