<?php

namespace App\Exceptions\Feenicia;

use RuntimeException;

/**
 * Excepción base para cualquier error de la API Feenicia.
 * Siempre incluye el responseCode para que el llamador pueda manejarlo.
 */
class FeeniciaException extends RuntimeException
{
    /**
     * @param string               $responseCode  Código de error de Feenicia (ej: 'A006', '51', 'S005')
     * @param string               $message       Mensaje legible
     * @param array<string, mixed> $body          Respuesta completa de Feenicia
     */
    public function __construct(
        public readonly string $responseCode,
        string $message = '',
        public readonly array $body = [],
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    /**
     * ¿La transacción fue rechazada por fondos insuficientes?
     */
    public function isInsufficientFunds(): bool
    {
        return $this->responseCode === '51';
    }

    /**
     * ¿La tarjeta fue rechazada/expirada?
     */
    public function isCardDeclined(): bool
    {
        return in_array($this->responseCode, ['05', '14', '33', '54', '62']);
    }

    /**
     * ¿Es un error de encriptación/firma? (implica revisar las llaves)
     */
    public function isSecurityError(): bool
    {
        return in_array($this->responseCode, ['S001', 'S002', 'S003', 'S004', 'S005', 'A006']);
    }

    /**
     * ¿La transacción ya fue procesada anteriormente?
     */
    public function isDuplicate(): bool
    {
        return in_array($this->responseCode, ['94', 'A031', 'A062', 'A063']);
    }

    /**
     * ¿Se puede reintentar en unos minutos?
     */
    public function isRetryable(): bool
    {
        return in_array($this->responseCode, ['91', '96', '90']);
    }
}
