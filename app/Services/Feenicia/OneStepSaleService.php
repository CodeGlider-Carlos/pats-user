<?php

namespace App\Services\Feenicia;

use App\DTO\Feenicia\OneStepSaleData;
use App\Exceptions\Feenicia\FeeniciaTimeoutException;
use Illuminate\Support\Facades\Log;

/**
 * Ejecuta una venta manual en un solo paso.
 *
 * Endpoint: POST /v1/atna/sale/oneStepSaleManual
 * Documento: IT-OF-030
 *
 * A diferencia del flujo de 5 pasos, este endpoint no genera orderId
 * ni requiere guardar firma ni recibo por separado.
 *
 * Uso:
 *   $service = app(OneStepSaleService::class);
 *
 *   $data = new OneStepSaleData(
 *       affiliation:     '9165713',
 *       amount:          '300.00',
 *       transactionDate: now()->timestamp * 1000,
 *       pan:             '4111111111111111',
 *       cardholderName:  'Juan Perez',
 *       cvv2:            '123',
 *       expDate:         '1225',
 *       userId:          'fenicia_qa',
 *   );
 *
 *   $result = $service->execute($data);
 *   // $result['transactionId'] y $result['authnum'] — guardarlos en BD
 */
class OneStepSaleService
{
    public function __construct(
        private readonly FeeniciaHttpClient   $http,
        private readonly FeeniciaCryptoService $crypto,
    ) {}

    /**
     * Ejecuta la venta de un paso.
     *
     * @param  OneStepSaleData          $data
     * @return array<string, mixed>     Respuesta completa de Feenicia
     *
     * @throws FeeniciaTimeoutException  Si hay timeout — el llamador debe enviar Reversal
     * @throws \App\Exceptions\Feenicia\FeeniciaException  Si responseCode != '00'
     */
    public function execute(OneStepSaleData $data): array
    {
        $payload = $data->toArray();

        // LOG TEMPORAL
        Log::channel('feenicia')->info('Payload antes de encriptar', $payload);

        $payload = $this->crypto->encryptFields(
            $payload,
            OneStepSaleData::encryptedFields()
        );

        return $this->http->post(
            config('feenicia.endpoints.sale_one_step'),
            $payload
        );
    }
}
