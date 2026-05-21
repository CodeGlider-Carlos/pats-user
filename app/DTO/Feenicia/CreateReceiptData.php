<?php

namespace App\DTO\Feenicia;

/**
 * Paso d) — Crear recibo (opcional).
 * Compartido por: Cash Sale, Recurring Billing, Manual Sale (5 pasos).
 * Endpoint: POST /receipt/receipt/CreateReceipt
 *
 * Nota: La mayoría de campos son siempre null o 0 según la documentación.
 * Solo OrderId y TransactionId son variables.
 */
class CreateReceiptData extends FeeniciaBaseData
{
    public function __construct(
        public readonly string $OrderId,
        public readonly int    $TransactionId,
        public readonly float  $Total                    = 0.0,
        public readonly mixed  $LegalEntityName          = null,
        public readonly mixed  $MerchantStreetNumColony  = null,
        public readonly mixed  $MerchantCityStateZipCode = null,
        public readonly mixed  $AffiliationId            = null,
        public readonly mixed  $LastDigitsCard           = null,
        public readonly mixed  $Base64ImgSignature       = null,
        public readonly mixed  $AuthNumber               = null,
        public readonly mixed  $OperationId              = null,
        public readonly mixed  $ControlNumber            = null,
        public readonly mixed  $NameInCard               = null,
        public readonly mixed  $DescriptionCard          = null,
        public readonly string $ReceiptDateTime          = '0001-01-01T00:00:00',
        public readonly mixed  $AID                      = null,
        public readonly mixed  $ARQC                     = null,
        public readonly mixed  $MensajeComercio          = null,
        public readonly mixed  $ClientLogoBase64Data     = null,
        public readonly mixed  $ClientLogoDataType       = null,
        public readonly bool   $SendUrlByMail            = false,
        public readonly float  $Propina                  = 0.0,
        public readonly mixed  $strMerchantId            = null,
    ) {}

    public static function encryptedFields(): array
    {
        return [];
    }
}
