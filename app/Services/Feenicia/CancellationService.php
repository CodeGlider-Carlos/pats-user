<?php

namespace App\Services\Feenicia;

use App\DTO\Feenicia\PostSaleData;
use App\Exceptions\Feenicia\FeeniciaException;

/**
 * Cancela una transacción existente.
 *
 * Endpoint: POST /v1/atna/cancel/manual
 * Documento: IT-OF-003
 *
 * Diferencias vs Refund:
 *  - La respuesta solo contiene responseCode (no objeto completo)
 *  - Se usa cuando la transacción aún no se ha liquidado
 */
class CancellationService
{
    public function __construct(
        private readonly FeeniciaHttpClient    $http,
        private readonly FeeniciaCryptoService $crypto,
    ) {}

    /**
     * @param  PostSaleData             $data
     * @return array<string, mixed>     Solo contiene responseCode
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
            config('feenicia.endpoints.cancellation'),
            $payload
        );
    }
}
