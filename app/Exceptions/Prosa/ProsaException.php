<?php

namespace App\Exceptions\Prosa;

use Exception;

/**
 * Error de negocio devuelto por OPPWA: el result.code no corresponde a un
 * pago exitoso/pendiente (rechazo de emisor, validación, riesgo, etc.).
 */
class ProsaException extends Exception
{
    public function __construct(
        string $message,
        public readonly string $resultCode = '',
        public readonly array $raw = [],
    ) {
        parent::__construct($message);
    }
}
