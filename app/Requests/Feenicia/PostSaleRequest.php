<?php

namespace App\Http\Requests\Feenicia;

use Illuminate\Foundation\Http\FormRequest;
use App\DTO\Feenicia\PostSaleData;

/**
 * Valida los campos comunes de Refund, Cancellation y Reversal.
 * Los tres endpoints comparten exactamente el mismo contrato de request.
 */
class PostSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'affiliation'     => ['required', 'string', 'max:15'],
            'amount'          => ['required', 'numeric', 'min:0.01'],
            'transactionDate' => ['required', 'integer'],
            'orderId'         => ['required', 'string'],
            'pan'             => ['required', 'string'],
            'cardholderName'  => ['required', 'string'],
            'expDate'         => ['required', 'string', 'size:4'],
            'authnum'         => ['required', 'string'],
            'transactionId'   => ['required', 'string'],
            'cvv2'            => ['nullable', 'string', 'min:3', 'max:4'],
        ];
    }

    public function toDTO(): PostSaleData
    {
        return new PostSaleData(
            affiliation:     $this->input('affiliation'),
            amount:          (float)  $this->input('amount'),
            transactionDate: (int)    $this->input('transactionDate'),
            orderId:         $this->input('orderId'),
            pan:             $this->input('pan'),
            cardholderName:  $this->input('cardholderName'),
            expDate:         $this->input('expDate'),
            authnum:         $this->input('authnum'),
            transactionId:   $this->input('transactionId'),
            cvv2:            $this->input('cvv2'),
        );
    }
}
