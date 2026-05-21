<?php

namespace App\DTO\Feenicia;

/**
 * Datos para el paso b) de Recurring Billing.
 * Endpoint: POST /v1/atna/sale/recurringCharge
 * Documento: IT-OF-017
 */
class RecurringBillingData extends FeeniciaBaseData
{
    /**
     * @param string $affiliation      Número de afiliación (máx 15 chars)
     * @param float  $amount           Monto del cobro
     * @param string $cardholderName   Nombre del tarjetahabiente — se encriptará
     * @param string $expDate          Fecha de expiración — se encriptará
     * @param string $pan              Número de tarjeta — se encriptará
     * @param string $contractNumber   Número de control propio (1-20 chars)
     * @param int    $transactionDate  Fecha en EPOCH (milliseconds)
     */
    public function __construct(
        public readonly string $affiliation,
        public readonly float  $amount,
        public readonly string $cardholderName,
        public readonly string $expDate,
        public readonly string $pan,
        public readonly string $contractNumber,
        public readonly int    $transactionDate,
    ) {}

    /**
     * @return string[]
     */
    public static function encryptedFields(): array
    {
        return ['pan', 'cardholderName', 'expDate'];
    }
}
