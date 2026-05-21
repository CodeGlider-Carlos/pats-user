<?php

namespace App\Services\Feenicia;

use App\DTO\Feenicia\PostSaleData;
use App\Exceptions\Feenicia\FeeniciaException;

/**
 * Ejecuta un reembolso sobre una transacción existente.
 *
 * Endpoint: POST /v1/atna/refund
 * Documento: IT-OF-018
 *
 * Diferencias vs Cancellation/Reversal:
 *  - La respuesta incluye el objeto completo (merchant, card, currency, etc.)
 *  - folio siempre llega null
 *  - cvv2 es opcional
 */
class RefundService
{
    public function __construct(
        private readonly FeeniciaHttpClient    $http,
        private readonly FeeniciaCryptoService $crypto,
    ) {}

    /**
     * @param  PostSaleData             $data
     * @return array<string, mixed>     Respuesta completa de Feenicia
     *
     * @throws FeeniciaException
     */
    public function execute(PostSaleData $data): array
    {
        $payload = $this->crypto->encryptFields(
            $data->toArray(),
            PostSaleData::encryptedFields()
        );

        return $this->http->post(
            config('feenicia.endpoints.refund'),
            $payload
        );
    }
}
