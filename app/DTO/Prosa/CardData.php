<?php

namespace App\DTO\Prosa;

use InvalidArgumentException;

/**
 * Datos de una tarjeta para una venta server-to-server con OPPWA.
 *
 * Mapea a los parámetros `card.*` y `paymentBrand` de la API.
 */
class CardData
{
    public function __construct(
        public readonly string $number,
        public readonly string $holder,
        public readonly string $expiryMonth,
        public readonly string $expiryYear,
        public readonly string $cvv,
    ) {}

    /**
     * Construye desde los campos de un formulario.
     *
     * @param  string  $expMonth  Mes en 1-2 dígitos (1-12)
     * @param  string  $expYear   Año en 2 o 4 dígitos
     */
    public static function fromForm(
        string $number,
        string $holder,
        string $expMonth,
        string $expYear,
        string $cvv,
    ): self {
        return new self(
            number:      preg_replace('/\D/', '', $number),
            holder:      self::normalizeHolder($holder),
            expiryMonth: str_pad(ltrim(preg_replace('/\D/', '', $expMonth), '0') ?: '0', 2, '0', STR_PAD_LEFT),
            expiryYear:  self::normalizeYear($expYear),
            cvv:         preg_replace('/\D/', '', $cvv),
        );
    }

    /**
     * Convierte el nombre del titular a ASCII puro (A-Z 0-9 espacio).
     *
     * OPPWA define `card.holder` como formato A128 (Latin/ASCII only).
     * Caracteres como á, é, ñ, ü provocan error 200.300.404 "invalid parameter".
     *
     * Estrategia: iconv TRANSLIT (ñ→n, á→a, etc.) → strstrip → mayúsculas.
     */
    private static function normalizeHolder(string $holder): string
    {
        $holder = trim($holder);

        // Intentar transliteración con iconv (convierte ñ→n, á→a, etc.)
        $translit = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $holder);
        if ($translit !== false && $translit !== '') {
            $holder = $translit;
        } else {
            // Fallback: eliminar todo lo que no sea ASCII imprimible
            $holder = preg_replace('/[^\x20-\x7E]/', '', $holder);
        }

        // Permitir solo A-Z, a-z, 0-9 y espacio; eliminar resto de caracteres especiales
        $holder = (string) preg_replace('/[^A-Za-z0-9 ]/', ' ', $holder);
        $holder = (string) preg_replace('/\s+/', ' ', trim($holder));

        return strtoupper($holder);
    }

    private static function normalizeYear(string $year): string
    {
        $year = preg_replace('/\D/', '', $year);

        if (strlen($year) === 2) {
            return '20' . $year;
        }

        if (strlen($year) === 4) {
            return $year;
        }

        throw new InvalidArgumentException('Año de expiración inválido.');
    }

    /**
     * Marca de la tarjeta inferida del BIN, como la espera OPPWA en `paymentBrand`.
     */
    public function brand(): string
    {
        $n = $this->number;

        return match (true) {
            str_starts_with($n, '4')                                  => 'VISA',
            (bool) preg_match('/^(5[1-5]|2[2-7])/', $n)               => 'MASTER',
            (bool) preg_match('/^3[47]/', $n)                          => 'AMEX',
            default                                                     => 'VISA',
        };
    }

    public function last4(): string
    {
        return substr($this->number, -4);
    }

    public function bin(): string
    {
        return substr($this->number, 0, 6);
    }

    /**
     * @return array<string, string>
     */
    public function toParams(): array
    {
        return [
            'paymentBrand'      => $this->brand(),
            'card.number'       => $this->number,
            'card.holder'       => $this->holder,
            'card.expiryMonth'  => $this->expiryMonth,
            'card.expiryYear'   => $this->expiryYear,
            'card.cvv'          => $this->cvv,
        ];
    }
}
