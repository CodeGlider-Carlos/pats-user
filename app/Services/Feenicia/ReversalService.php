<?php

namespace App\Services\Feenicia;

use App\DTO\Feenicia\PostSaleData;
use App\Exceptions\Feenicia\FeeniciaException;
use App\Exceptions\Feenicia\FeeniciaTimeoutException;
use Illuminate\Support\Facades\Log;

/**
 * Envía un reverso para cancelar una transacción en estado indeterminado.
 *
 * Endpoint: POST /v1/atna/reversal
 * Documento: IT-OF-027
 *
 * ⚠️ CRÍTICO (IT-OF-017):
 * Debe ejecutarse INMEDIATAMENTE cuando hay un timeout en cualquier
 * transacción de venta. Si no se envía el reversal, puede quedar un
 * cobro duplicado o en estado desconocido.
 *
 * Diferencias vs Cancellation:
 *  - Se usa exclusivamente ante timeouts o comportamiento inesperado
 *  - La respuesta solo contiene responseCode
 */
class ReversalService
{
    public function __construct(
        private readonly FeeniciaHttpClient    $http,
        private readonly FeeniciaCryptoService $crypto,
    ) {}

    /**
     * Reversal estándar a partir de datos conocidos.
     *
     * @param  PostSaleData             $data
     * @return array<string, mixed>
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
            config('feenicia.endpoints.reversal'),
            $payload
        );
    }

    /**
     * Reversal automático ante un timeout.
     * Llamado desde el controller cuando atrapa FeeniciaTimeoutException.
     *
     * Reutiliza el payload original del request fallido para construir
     * el reversal con los mismos datos de la transacción.
     *
     * @param  FeeniciaTimeoutException $e  Excepción del timeout original
     * @return array<string, mixed>
     *
     * @throws FeeniciaException
     */
    public function executeFromTimeout(FeeniciaTimeoutException $e): array
    {
        Log::channel('feenicia')->warning('Enviando reversal por timeout', [
            'endpoint'       => $e->endpoint,
            'original_payload' => array_keys($e->payload),
        ]);

        // El payload del timeout ya tiene los campos encriptados
        // (se encriptaron antes de enviarse), así que los usamos directamente
        return $this->http->post(
            config('feenicia.endpoints.reversal'),
            $e->getReversalPayload()
        );
    }
}
