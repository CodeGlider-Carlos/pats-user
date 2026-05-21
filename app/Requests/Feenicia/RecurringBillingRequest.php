<?php

namespace App\Http\Requests\Feenicia;

use Illuminate\Foundation\Http\FormRequest;
use App\DTO\Feenicia\RecurringBillingData;
use App\DTO\Feenicia\SaleOrderData;

class RecurringBillingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // ── Cobro recurrente ──
            'affiliation'     => ['required', 'string', 'max:15'],
            'amount'          => ['required', 'numeric', 'min:0.01'],
            'cardholderName'  => ['required', 'string'],
            'expDate'         => ['required', 'string', 'size:4'],
            'pan'             => ['required', 'string'],
            'contractNumber'  => ['required', 'string', 'min:1', 'max:20'],
            'transactionDate' => ['required', 'integer'],

            // ── Orden (paso a) ──
            'description'     => ['nullable', 'string', 'max:100'],

            // ── Recibo (opcional) ──
            'sendReceiptTo'   => ['nullable', 'email'],
        ];
    }

    public function toBillingDTO(): RecurringBillingData
    {
        return new RecurringBillingData(
            affiliation:     $this->input('affiliation'),
            amount:          (float)  $this->input('amount'),
            cardholderName:  $this->input('cardholderName'),
            expDate:         $this->input('expDate'),
            pan:             $this->input('pan'),
            contractNumber:  $this->input('contractNumber'),
            transactionDate: (int) $this->input('transactionDate'),
        );
    }

    public function toOrderDTO(): SaleOrderData
    {
        $amount = (float) $this->input('amount');

        return new SaleOrderData(
            amount:   $amount,
            items:    SaleOrderData::singleItem($amount, $this->input('description', 'Cobro recurrente')),
            merchant: config('feenicia.merchant'),
            userId:   config('feenicia.user'),
        );
    }
}
