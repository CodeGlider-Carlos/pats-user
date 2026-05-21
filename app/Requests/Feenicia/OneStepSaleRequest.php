<?php

namespace App\Http\Requests\Feenicia;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Valida el request HTTP antes de pasarlo al OneStepSaleService.
 * Si la validación falla, Laravel responde automáticamente con 422.
 */
class OneStepSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Ajusta según tu sistema de autenticación
        return true;
    }

    /**
     * @return array<string, mixed>
     */
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
            'expDate'         => ['required', 'string', 'size:4'],   // MMYY
            'userId'          => ['required', 'string', 'max:32'],

            // ── Opcionales ──
            'tip'             => ['nullable', 'numeric', 'min:0'],
            'terminal'        => ['nullable', 'string', 'max:100'],

            // ── Diferimiento (si se envía, todos sus campos son requeridos) ──
            'deferralData'                => ['nullable', 'array'],
            'deferralData.payments'       => ['required_with:deferralData', 'string', 'in:03,06,09,12,15,18'],
            'deferralData.deferral'       => ['required_with:deferralData', 'string', 'in:00'],
            'deferralData.planType'       => ['required_with:deferralData', 'string', 'in:03'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'affiliation.max'            => 'El número de afiliación no puede superar 15 caracteres.',
            'amount.min'                 => 'El monto debe ser mayor a cero.',
            'expDate.size'               => 'La fecha de expiración debe tener 4 caracteres (MMYY).',
            'deferralData.payments.in'   => 'Los pagos diferidos deben ser: 03, 06, 09, 12, 15 o 18.',
            'cvv2.min'                   => 'El CVV2 debe tener al menos 3 dígitos.',
        ];
    }

    /**
     * Construye el DTO a partir del request validado.
     * Se llama desde el controller para evitar lógica en el controller.
     */
    public function toDTO(): \App\DTO\Feenicia\OneStepSaleData
    {
        return new \App\DTO\Feenicia\OneStepSaleData(
            affiliation:     $this->input('affiliation'),
            amount:          (string) $this->input('amount'),
            transactionDate: (int)    $this->input('transactionDate'),
            pan:             $this->input('pan'),
            cardholderName:  $this->input('cardholderName'),
            cvv2:            $this->input('cvv2'),
            expDate:         $this->input('expDate'),
            userId:          $this->input('userId'),
            tip:             $this->input('tip', '0.0'),
            terminal:        $this->input('terminal'),
            deferralData:    $this->input('deferralData'),
        );
    }
}
