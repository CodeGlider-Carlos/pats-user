<?php

namespace App\DTO\Feenicia;

/**
 * Datos para el paso b) de una venta en efectivo.
 * Endpoint: POST /v1/atna/sale/cash
 * Documento: IT-OF-004
 *
 * Nota: Cash Sale requiere 5 pasos. Este DTO cubre el paso principal (b).
 * Los pasos a), c), d) y e) usan sus propios DTOs compartidos.
 */
class CashSaleData extends FeeniciaBaseData
{
    /**
     * @param string      $affiliation      Número de afiliación (máx 15 chars)
     * @param string      $amount           Monto total
     * @param int         $transactionDate  EPOCH en milliseconds
     * @param string      $orderId          Obtenido del paso a) GenerateSaleOrder
     * @param string      $cardholderName   Nombre del tarjetahabiente (NO se encripta en cash)
     * @param float       $tip              Propina (independiente del amount)
     * @param array|null  $geoData          Geolocalización ['latitude' => ..., 'longitude' => ...]
     */
    public function __construct(
        public readonly string  $affiliation,
        public readonly string  $amount,
        public readonly int     $transactionDate,
        public readonly string  $orderId,
        public readonly string  $cardholderName,
        public readonly float   $tip     = 0,
        public readonly ?array  $geoData = null,
    ) {}

    /**
     * Cash Sale NO encripta campos — es venta en efectivo.
     *
     * @return string[]
     */
    public static function encryptedFields(): array
    {
        return [];
    }
}
