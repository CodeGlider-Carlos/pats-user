<?php

namespace App\DTO\Feenicia\Tokenization;

use App\DTO\Feenicia\FeeniciaBaseData;

/**
 * Genera un token para una tarjeta.
 * Endpoint: POST /balder/token/generateToken
 *
 * Los campos sensibles deben ir encriptados.
 */
class GenerateTokenData extends FeeniciaBaseData
{
    /**
     * @param string      $pan             Número de tarjeta — se encriptará
     * @param string      $cardholderName  Nombre del titular — se encriptará
     * @param string      $expDate         Fecha de expiración MMYY — se encriptará
     * @param string      $cvv2            CVV2 — se encriptará
     * @param string      $affiliation     Número de afiliación
     * @param string|null $alias           Alias amigable para la tarjeta (opcional)
     */
    public function __construct(
        public readonly string  $pan,
        public readonly string  $cardholderName,
        public readonly string  $expDate,
        public readonly string  $cvv2,
        public readonly string  $affiliation,
        public readonly ?string $alias = null,
    ) {}

    public static function encryptedFields(): array
    {
        return ['pan', 'cardholderName', 'expDate', 'cvv2'];
    }
}
