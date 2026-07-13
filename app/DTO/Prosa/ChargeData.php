<?php

namespace App\DTO\Prosa;

/**
 * Parámetros de una venta (paymentType DB) server-to-server con OPPWA.
 */
class ChargeData
{
    public function __construct(
        public readonly CardData $card,
        public readonly float $amount,
        public readonly string $currency = 'MXN',
        public readonly string $paymentType = 'DB',
        public readonly ?string $merchantTransactionId = null,
        public readonly ?string $descriptor = null,
        public readonly bool $createRegistration = false,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toParams(): array
    {
        $params = array_merge([
            'amount'      => number_format($this->amount, 2, '.', ''),
            'currency'    => $this->currency,
            'paymentType' => $this->paymentType,
        ], $this->card->toParams());

        if ($this->merchantTransactionId !== null) {
            $params['merchantTransactionId'] = $this->merchantTransactionId;
        }

        if ($this->descriptor !== null) {
            $params['descriptor'] = $this->descriptor;
        }

        if ($this->createRegistration) {
            $params['createRegistration'] = 'true';
        }

        return $params;
    }
}
