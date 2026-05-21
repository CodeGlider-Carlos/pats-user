<?php

namespace App\Services\Feenicia;

use App\DTO\Feenicia\CashSaleData;
use App\DTO\Feenicia\SaleOrderData;
use App\DTO\Feenicia\SaveSignatureData;
use App\DTO\Feenicia\CreateReceiptData;
use App\DTO\Feenicia\SendReceiptData;
use App\Exceptions\Feenicia\FeeniciaException;
use App\Exceptions\Feenicia\FeeniciaTimeoutException;
use Illuminate\Support\Facades\Log;

/**
 * Ejecuta una venta en efectivo en 5 pasos.
 *
 * Endpoint principal: POST /v1/atna/sale/cash
 * Documento: IT-OF-004
 *
 * Flujo:
 *  a) Generar orden    → /receipt/order/create       → orderId
 *  b) Venta efectivo   → /v1/atna/sale/cash          → transactionId, authnum
 *  c) Guardar firma    → /receipt/signature/save
 *  d) Crear recibo     → /receipt/receipt/CreateReceipt  (opcional)
 *  e) Enviar recibo    → /receipt/receipt/SendReceipt    (opcional)
 *
 * Diferencias vs Recurring Billing:
 *  - No requiere PAN, CVV ni expDate (es efectivo)
 *  - Sí requiere geoData (latitud/longitud) opcional
 *  - cardholderName NO se encripta en este flujo
 *
 * Uso básico:
 *   $result = $service->execute($cashData, $orderData);
 *
 * Con recibo:
 *   $result = $service->execute($cashData, $orderData, 'cliente@email.com');
 */
class CashSaleService
{
    public function __construct(
        private readonly FeeniciaHttpClient    $http,
        private readonly FeeniciaCryptoService $crypto,
        private readonly RecurringBillingService $sharedSteps, // reutiliza pasos a, c, d, e
    ) {}

    /**
     * Ejecuta el flujo completo de venta en efectivo.
     *
     * @param  CashSaleData   $cash           Datos del paso b)
     * @param  SaleOrderData  $order          Datos del paso a)
     * @param  string|null    $sendReceiptTo  Email para enviar recibo (opcional)
     *
     * @return array{
     *   orderId: int,
     *   transactionId: int,
     *   authnum: string,
     *   approved: bool,
     *   amount: float,
     * }
     *
     * @throws FeeniciaTimeoutException  Si hay timeout en paso b)
     * @throws FeeniciaException         Si cualquier paso falla
     */
    public function execute(
        CashSaleData  $cash,
        SaleOrderData $order,
        ?string       $sendReceiptTo = null,
    ): array {

        // ── Paso a) Generar orden ──────────────────────────────
        $orderResult = $this->sharedSteps->createOrder($order);
        $orderId     = $orderResult['orderId'];

        Log::channel('feenicia')->info('CashSale a) orden creada', [
            'orderId' => $orderId,
        ]);

        // ── Actualizar orderId en el DTO del paso b) ──────────
        // CashSaleData necesita el orderId obtenido en el paso a)
        $cashWithOrder = new CashSaleData(
            affiliation:     $cash->affiliation,
            amount:          $cash->amount,
            transactionDate: $cash->transactionDate,
            orderId:         (string) $orderId,
            cardholderName:  $cash->cardholderName,
            tip:             $cash->tip,
            geoData:         $cash->geoData,
        );

        // ── Paso b) Venta en efectivo ──────────────────────────
        // ⚠️ Si lanza FeeniciaTimeoutException → el llamador debe enviar Reversal
        $saleResult    = $this->cashCharge($cashWithOrder);
        $transactionId = $saleResult['transactionId'];
        $authnum       = $saleResult['authnum'];

        Log::channel('feenicia')->info('CashSale b) venta realizada', [
            'transactionId' => $transactionId,
            'authnum'       => $authnum,
        ]);

        // ── Paso c) Guardar firma ──────────────────────────────
        $this->sharedSteps->saveSignature(new SaveSignatureData(
            orderId:         (string) $orderId,
            transactionId:   $transactionId,
            authnum:         $authnum,
            transactionDate: now()->format('Y-n-j H:i'),
            panTermination:  '0000', // efectivo no tiene PAN
            affiliation:     $cash->affiliation,
            merchant:        config('feenicia.merchant'),
        ));

        Log::channel('feenicia')->info('CashSale c) firma guardada');

        $receiptId = null;

        // ── Pasos d) y e) opcionales ───────────────────────────
        if ($sendReceiptTo !== null) {
            $receiptResult = $this->sharedSteps->createReceipt(new CreateReceiptData(
                OrderId:       (string) $orderId,
                TransactionId: $transactionId,
            ));

            $receiptId = $receiptResult['receiptId'] ?? null;

            if ($receiptId) {
                $this->sharedSteps->sendReceipt(new SendReceiptData(
                    receiptId: $receiptId,
                    Email:     [$this->crypto->encrypt($sendReceiptTo)],
                ));

                Log::channel('feenicia')->info('CashSale e) recibo enviado', [
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
            'tip'           => $saleResult['tip'] ?? $cash->tip,
            'receiptId'     => $receiptId,
        ];
    }

    // ──────────────────────────────────────────────
    //  Paso b) interno
    // ──────────────────────────────────────────────

    /**
     * Paso b) — Ejecuta la venta en efectivo.
     * Cash Sale NO encripta campos (sin PAN/CVV).
     *
     * @throws FeeniciaTimeoutException
     */
    private function cashCharge(CashSaleData $data): array
    {
        return $this->http->post(
            config('feenicia.endpoints.sale_cash'),
            $data->toArray()
        );
    }
}
