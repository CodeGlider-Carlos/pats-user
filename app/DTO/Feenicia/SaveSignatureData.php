<?php

namespace App\DTO\Feenicia;

/**
 * Paso c) — Guardar firma de la transacción.
 * Compartido por: Cash Sale, Recurring Billing, Manual Sale (5 pasos).
 * Endpoint: POST /receipt/signature/save
 */
class SaveSignatureData extends FeeniciaBaseData
{
    /**
     * @param string $orderId         Del paso a)
     * @param int    $transactionId   Del paso b)
     * @param string $authnum         Del paso b)
     * @param string $transactionDate Formato: YYYY-MM-dd HH:mm
     * @param string $panTermination  Últimos 4 dígitos de la tarjeta
     * @param string $affiliation     Número de afiliación
     * @param string $merchant        MerchantId de 16 dígitos
     */
    public function __construct(
        public readonly string $orderId,
        public readonly int    $transactionId,
        public readonly string $authnum,
        public readonly string $transactionDate,
        public readonly string $panTermination,
        public readonly string $affiliation,
        public readonly string $merchant,
    ) {}

    public static function encryptedFields(): array
    {
        return [];
    }
}
