<?php

namespace App\DTO\Feenicia;

/**
 * DTO compartido para Refund, Cancellation y Reversal.
 * Los tres endpoints tienen exactamente el mismo contrato de request.
 *
 * Documentos: IT-OF-018 (Refund), IT-OF-003 (Cancellation), IT-OF-027 (Reversal)
 */
class PostSaleData extends FeeniciaBaseData
{
    /**
     * @param string      $affiliation      Número de afiliación (máx 15 chars)
     * @param float       $amount           Monto de la transacción original
     * @param int         $transactionDate  Fecha actual en EPOCH (milliseconds)
     * @param string      $orderId          OrderId de la venta original
     * @param string      $pan              PAN de la venta original — se encriptará
     * @param string      $cardholderName   Nombre de la venta original — se encriptará
     * @param string      $expDate          Fecha de exp. de la venta original — se encriptará
     * @param string      $authnum          Número de autorización de la venta original
     * @param string      $transactionId    TransactionId de la venta original
     * @param string|null $cvv2             CVV2 (opcional) — se encriptará si se envía
     */
    public function __construct(
        public readonly string  $affiliation,
        public readonly float   $amount,
        public readonly int     $transactionDate,
        public readonly string  $orderId,
        public readonly string  $pan,
        public readonly string  $cardholderName,
        public readonly string  $expDate,
        public readonly string  $authnum,
        public readonly string  $transactionId,
        public readonly ?string $cvv2 = null,
    ) {}

    /**
     * @return string[]
     */
    public static function encryptedFields(): array
    {
        return ['pan', 'cardholderName', 'expDate', 'cvv2'];
    }
}
