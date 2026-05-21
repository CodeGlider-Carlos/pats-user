<?php

namespace App\DTO\Feenicia;

class OneStepSaleData extends FeeniciaBaseData
{
    public function __construct(
        public readonly string  $affiliation,
        public readonly float   $amount,
        public readonly int     $transactionDate,
        public readonly string  $pan,
        public readonly string  $cardholderName,
        public readonly string  $cvv2,
        public readonly string  $expDate,
        public readonly ?string $userId       = null,
        public readonly ?string $tip          = '0.0',
        public readonly ?string $terminal     = null,
        public readonly ?array  $deferralData = null,
    ) {}

    public static function encryptedFields(): array
    {
        return ['pan', 'cardholderName', 'cvv2', 'expDate'];
    }
}