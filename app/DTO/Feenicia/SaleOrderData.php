<?php

namespace App\DTO\Feenicia;

/**
 * Paso a) — Generar orden de venta.
 * Compartido por: Cash Sale, Recurring Billing, Manual Sale (5 pasos).
 * Endpoint: POST /receipt/order/create
 */
class SaleOrderData extends FeeniciaBaseData
{
    public function __construct(
        public readonly float  $amount,
        public readonly array  $items,
        public readonly string $merchant,
        public readonly string $userId,
    ) {}

    /**
     * Helper para construir un item estándar de una sola línea.
     */
    public static function singleItem(float $amount, string $description = 'Producto'): array
    {
        return [[
            'Quantity'    => '1',
            'description' => $description,
            'unitPrice'   => (string) $amount,
            'amount'      => $amount,
            'Id'          => 0,
        ]];
    }

    public static function encryptedFields(): array
    {
        return [];
    }
}
