<?php

namespace App\Http\Requests\Feenicia;

use Illuminate\Foundation\Http\FormRequest;
use App\DTO\Feenicia\CashSaleData;
use App\DTO\Feenicia\SaleOrderData;

class CashSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // ── Obligatorios ──
            'affiliation'     => ['required', 'string', 'max:15'],
            'amount'          => ['required', 'numeric', 'min:0.01'],
            'transactionDate' => ['required', 'integer'],
            'cardholderName'  => ['required', 'string', 'max:100'],

            // ── Opcionales ──
            'tip'             => ['nullable', 'numeric', 'min:0'],
            'description'     => ['nullable', 'string', 'max:100'],
            'sendReceiptTo'   => ['nullable', 'email'],

            // ── Geolocalización (opcional) ──
            'geoData'           => ['nullable', 'array'],
            'geoData.latitude'  => ['required_with:geoData', 'numeric'],
            'geoData.longitude' => ['required_with:geoData', 'numeric'],
        ];
    }

    public function toCashDTO(): CashSaleData
    {
        return new CashSaleData(
            affiliation:     $this->input('affiliation'),
            amount:          $this->input('amount'),
            transactionDate: (int) $this->input('transactionDate'),
            orderId:         '', // se llena en el servicio tras el paso a)
            cardholderName:  $this->input('cardholderName'),
            tip:             (float) $this->input('tip', 0),
            geoData:         $this->input('geoData'),
        );
    }

    public function toOrderDTO(): SaleOrderData
    {
        $amount = (float) $this->input('amount');

        return new SaleOrderData(
            amount:   $amount,
            items:    SaleOrderData::singleItem($amount, $this->input('description', 'Venta en efectivo')),
            merchant: config('feenicia.merchant'),
            userId:   config('feenicia.user'),
        );
    }
}
