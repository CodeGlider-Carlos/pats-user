<?php

namespace App\DTO\Feenicia\Tokenization;

use App\DTO\Feenicia\FeeniciaBaseData;

/**
 * Ejecuta una venta usando un token de tarjeta guardado.
 * Endpoint: POST /balder/token/saleToken
 *
 * No requiere PAN ni CVV — solo el token previamente generado.
 */
class SaleTokenData extends FeeniciaBaseData
{
    /**
     * @param string $token           Token de tarjeta generado previamente
     * @param float  $amount          Monto de la venta
     * @param string $affiliation     Número de afiliación
     * @param int    $transactionDate EPOCH en milliseconds
     * @param string $cvv2            CVV2 — se encriptará (requerido por seguridad)
     * @param float  $tip             Propina (opcional)
     */
    public function __construct(
        public readonly string $token,
        public readonly float  $amount,
        public readonly string $affiliation,
        public readonly int    $transactionDate,
        public readonly string $cvv2,
        public readonly float  $tip = 0,
    ) {}

    public static function encryptedFields(): array
    {
        return ['cvv2'];
    }
}
