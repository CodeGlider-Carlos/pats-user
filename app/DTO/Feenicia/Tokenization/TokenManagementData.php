<?php

namespace App\DTO\Feenicia\Tokenization;

use App\DTO\Feenicia\FeeniciaBaseData;

/**
 * Cancela una tarjeta tokenizada.
 * Endpoint: POST /balder/token/cancelCard
 */
class CancelCardData extends FeeniciaBaseData
{
    public function __construct(
        public readonly string $token,
        public readonly string $affiliation,
    ) {}

    public static function encryptedFields(): array { return []; }
}


/**
 * Actualiza los datos de una tarjeta tokenizada.
 * Endpoint: POST /balder/token/updateCard
 */
class UpdateCardData extends FeeniciaBaseData
{
    public function __construct(
        public readonly string  $token,
        public readonly string  $affiliation,
        public readonly ?string $expDate = null, // se encriptará si se envía
        public readonly ?string $alias   = null,
    ) {}

    public static function encryptedFields(): array
    {
        return ['expDate'];
    }
}


/**
 * Elimina una tarjeta tokenizada.
 * Endpoint: POST /balder/token/deleteCard
 */
class DeleteCardData extends FeeniciaBaseData
{
    public function __construct(
        public readonly string $token,
        public readonly string $affiliation,
    ) {}

    public static function encryptedFields(): array { return []; }
}


/**
 * Reverso de una venta con token.
 * Endpoint: POST /balder/token/reversalSale
 */
class TokenReversalData extends FeeniciaBaseData
{
    public function __construct(
        public readonly string $token,
        public readonly string $affiliation,
        public readonly float  $amount,
        public readonly int    $transactionDate,
        public readonly string $transactionId,
        public readonly string $authnum,
    ) {}

    public static function encryptedFields(): array { return []; }
}


/**
 * Reembolso de una venta con token.
 * Endpoint: POST /balder/token/refundTx
 */
class TokenRefundData extends FeeniciaBaseData
{
    public function __construct(
        public readonly string $token,
        public readonly string $affiliation,
        public readonly float  $amount,
        public readonly int    $transactionDate,
        public readonly string $transactionId,
        public readonly string $authnum,
    ) {}

    public static function encryptedFields(): array { return []; }
}
