<?php

namespace App\Http\Requests\Feenicia;

use Illuminate\Foundation\Http\FormRequest;
use App\DTO\Feenicia\OneStepSaleData;

class OneStepSaleRequest extends FormRequest
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
            'pan'             => ['required', 'string'],
            'cardholderName'  => ['required', 'string', 'max:100'],
            'cvv2'            => ['required', 'string', 'min:3', 'max:4'],
            'expDate'         => ['required', 'string', 'size:4'],

            // ── Opcionales ──
            'userId'          => ['nullable', 'string', 'max:32'],
            'tip'             => ['nullable', 'numeric', 'min:0'],
            'terminal'        => ['nullable', 'string', 'max:100'],

            // ── Diferimiento ──
            'deferralData'          => ['nullable', 'array'],
            'deferralData.payments' => ['required_with:deferralData', 'string', 'in:03,06,09,12,15,18'],
            'deferralData.deferral' => ['required_with:deferralData', 'string', 'in:00'],
            'deferralData.planType' => ['required_with:deferralData', 'string', 'in:03'],
        ];
    }

    public function messages(): array
    {
        return [
            'affiliation.max'          => 'El número de afiliación no puede superar 15 caracteres.',
            'amount.min'               => 'El monto debe ser mayor a cero.',
            'expDate.size'             => 'La fecha de expiración debe tener 4 caracteres (MMYY).',
            'deferralData.payments.in' => 'Los pagos diferidos deben ser: 03, 06, 09, 12, 15 o 18.',
            'cvv2.min'                 => 'El CVV2 debe tener al menos 3 dígitos.',
        ];
    }

    public function toDTO(): OneStepSaleData
    {
        return new OneStepSaleData(
            affiliation:     $this->input('affiliation'),
            amount: (float) $this->input('amount'),
            transactionDate: (int) $this->input('transactionDate'),
            pan:             $this->input('pan'),
            cardholderName:  $this->input('cardholderName'),
            cvv2:            $this->input('cvv2'),
            expDate:         $this->input('expDate'),
            userId:          $this->input('userId', config('feenicia.user')),
            tip:             $this->input('tip', '0.0'),
            terminal:        $this->input('terminal'),
            deferralData:    $this->input('deferralData'),
        );
    }
}