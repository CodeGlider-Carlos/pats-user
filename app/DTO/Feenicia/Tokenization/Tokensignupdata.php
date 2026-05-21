<?php

namespace App\DTO\Feenicia\Tokenization;

use App\DTO\Feenicia\FeeniciaBaseData;

class TokenSignupData extends FeeniciaBaseData
{
    public function __construct(
        public readonly string $merchant,
        public readonly string $affiliation,
        public readonly string $userId,    // ← era 'user', ahora 'userId'
    ) {}

    public static function encryptedFields(): array
    {
        return [];
    }
}