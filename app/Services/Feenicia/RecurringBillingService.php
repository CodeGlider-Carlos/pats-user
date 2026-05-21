<?php

namespace App\Services\Feenicia;

use App\DTO\Feenicia\RecurringBillingData;
use App\DTO\Feenicia\SaleOrderData;
use App\DTO\Feenicia\SaveSignatureData;
use App\DTO\Feenicia\CreateReceiptData;
use App\DTO\Feenicia\SendReceiptData;
use App\Exceptions\Feenicia\FeeniciaException;
use App\Exceptions\Feenicia\FeeniciaTimeoutException;
use Illuminate\Support\Facades\Log;

/**
 * Ejecuta un cobro recurrente en 5 pasos.
 *
 * Endpoint principal: POST /v1/atna/sale/recurringCharge
 * Documento: IT-OF-017
 *
 * Flujo:
 *  a) Generar orden      → /receipt/order/create          → orderId
 *  b) Cobro recurrente   → /v1/atna/sale/recurringCharge  → transactionId, authnum
 *  c) Guardar firma      → /receipt/signature/save
 *  d) Crear recibo       → /receipt/receipt/CreateReceipt  (opcional)
 *  e) Enviar recibo      → /receipt/receipt/SendReceipt    (opcional)
 *
 * ⚠️ Si el paso b) falla por timeout → enviar Reversal inmediatamente.
 *
 * Uso básico (solo pasos a, b, c):
 *
 *   $result = $service->execute($billingData, $orderData);
 *
 * Uso completo con recibo:
 *
 *   $result = $service->execute(
 *       billing:        $billingData,
 *       order:          $orderData,
 *       sendReceiptTo:  'cliente@email.com',
 *   );
 */
class RecurringBillingService
{
    public function __construct(
        private readonly FeeniciaHttpClient    $http,
        private readonly FeeniciaCryptoService $crypto,
    ) {}

    /**
     * Ejecuta el flujo completo de cobro recurrente.
     *
     * @param  RecurringBillingData $billing        Datos del cobro (paso b)
     * @param  SaleOrderData        $order          Datos de la orden (paso a)
     * @param  string|null          $sendReceiptTo  Email para enviar recibo (pasos d+e, opcional)
     *
     * @return array{
     *   orderId: int,
     *   transactionId: int,
     *   authnum: string,
     *   approved: bool,
     *   amount: float,
     *   card: array,
     *   receiptId: string|null,
     * }
     *
     * @throws FeeniciaTimeoutException  Si hay timeout en paso b) — el llamador debe hacer Reversal
     * @throws FeeniciaException         Si cualquier paso falla
     */
    public function execute(
        RecurringBillingData $billing,
        SaleOrderData        $order,
        ?string              $sendReceiptTo = null,
    ): array {

        // ── Paso a) Generar orden ──────────────────────────────
        $orderResult = $this->createOrder($order);
        $orderId     = $orderResult['orderId'];

        Log::channel('feenicia')->info('Recurring a) orden creada', ['orderId' => $orderId]);

        // ── Paso b) Cobro recurrente ───────────────────────────
        // ⚠️ Si este paso lanza FeeniciaTimeoutException, el llamador
        // debe enviar un Reversal con los datos del cobro.
        $saleResult    = $this->charge($billing);
        $transactionId = $saleResult['transactionId'];
        $authnum       = $saleResult['authnum'];
        $last4         = $saleResult['card']['last4Digits'] ?? '0000';

        Log::channel('feenicia')->info('Recurring b) cobro realizado', [
            'transactionId' => $transactionId,
            'authnum'       => $authnum,
        ]);

        // ── Paso c) Guardar firma ──────────────────────────────
        $this->saveSignature(new SaveSignatureData(
            orderId:         (string) $orderId,
            transactionId:   $transactionId,
            authnum:         $authnum,
            transactionDate: now()->format('Y-n-j H:i'),
            panTermination:  $last4,
            affiliation:     $billing->affiliation,
            merchant:        config('feenicia.merchant'),
        ));

        Log::channel('feenicia')->info('Recurring c) firma guardada');

        $receiptId = null;

        // ── Pasos d) y e) opcionales ───────────────────────────
        if ($sendReceiptTo !== null) {
            $receiptResult = $this->createReceipt(new CreateReceiptData(
                OrderId:       (string) $orderId,
                TransactionId: $transactionId,
            ));

            $receiptId = $receiptResult['receiptId'] ?? null;

            if ($receiptId) {
                $this->sendReceipt(new SendReceiptData(
                    receiptId: $receiptId,
                    Email:     [$this->crypto->encrypt($sendReceiptTo)],
                ));

                Log::channel('feenicia')->info('Recurring e) recibo enviado', [
                    'receiptId' => $receiptId,
                ]);
            }
        }

        return [
            'orderId'       => $orderId,
            'transactionId' => $transactionId,
            'authnum'       => $authnum,
            'approved'      => $saleResult['approved'] ?? true,
            'amount'        => $saleResult['amount'],
            'card'          => $saleResult['card'],
            'folio'         => $saleResult['folio'] ?? $billing->contractNumber,
            'receiptId'     => $receiptId,
        ];
    }

    // ──────────────────────────────────────────────
    //  Pasos individuales (también públicos para
    //  poder reutilizarlos en CashSaleService)
    // ──────────────────────────────────────────────

    /**
     * Paso a) — Crea la orden de venta.
     *
     * @return array{orderId: int, responseCode: string}
     */
    public function createOrder(SaleOrderData $data): array
    {
        return $this->http->post(
            config('feenicia.endpoints.create_order'),
            $data->toArray()
        );
    }

    /**
     * Paso b) — Ejecuta el cobro recurrente.
     *
     * @throws FeeniciaTimeoutException
     */
    public function charge(RecurringBillingData $data): array
    {
        $payload = $this->crypto->encryptFields(
            $data->toArray(),
            RecurringBillingData::encryptedFields()
        );

        return $this->http->post(
            config('feenicia.endpoints.sale_recurring'),
            $payload
        );
    }

    /**
     * Paso c) — Guarda la firma de la transacción.
     */
    public function saveSignature(SaveSignatureData $data): array
    {
        return $this->http->post(
            config('feenicia.endpoints.save_signature'),
            $data->toArray()
        );
    }

    /**
     * Paso d) — Crea el recibo (opcional).
     *
     * @return array{receiptId: string, responseEmail: string}
     */
    public function createReceipt(CreateReceiptData $data): array
    {
        return $this->http->post(
            config('feenicia.endpoints.create_receipt'),
            $data->toArray()
        );
    }

    /**
     * Paso e) — Envía el recibo por email (opcional).
     *
     * @return array{Base64Pdf: string, responseCode: string}
     */
    public function sendReceipt(SendReceiptData $data): array
    {
        // El email ya viene encriptado desde el llamador
        return $this->http->post(
            config('feenicia.endpoints.send_receipt'),
            $data->toArray()
        );
    }
}
