<?php

namespace App\Exceptions\Prosa;

use Exception;
use Throwable;

/**
 * Timeout de conexión con OPPWA. El estado real del pago es desconocido:
 * el llamador debe consultar GET /v1/payments/{id} (si tiene un id) o tratar
 * la orden como TIMEOUT y conciliar después.
 *
 * @param array{path?: string, params?: array<string, mixed>} $context
 */
class ProsaTimeoutException extends Exception
{
    public function __construct(
        string $message = 'Timeout al comunicarse con la pasarela.',
        public readonly array $context = [],
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }
}
