<?php

namespace App\Exceptions\Feenicia;

/**
 * Se lanza cuando hay un timeout en la comunicación con Feenicia.
 *
 * ⚠️ IMPORTANTE (IT-OF-017):
 * Cuando ocurre un timeout, NO se sabe si la transacción fue procesada o no.
 * El sistema que atrape esta excepción DEBE enviar un Reversal inmediatamente.
 *
 * Ejemplo de manejo:
 *
 *   try {
 *       $result = $this->oneStepSaleService->execute($data);
 *   } catch (FeeniciaTimeoutException $e) {
 *       // Enviar reversal con los datos originales
 *       $this->reversalService->execute($e->getReversalPayload());
 *       throw $e; // propagar para que el controller responda 504
 *   }
 */
class FeeniciaTimeoutException extends FeeniciaException
{
    /**
     * @param string               $endpoint  Endpoint que falló
     * @param array<string, mixed> $payload   Payload original del request (para el reversal)
     */
    public function __construct(
        public readonly string $endpoint,
        public readonly array  $payload,
        ?\Throwable $previous = null,
    ) {
        parent::__construct(
            responseCode: 'TIMEOUT',
            message:      "Feenicia: timeout en {$endpoint}. Enviar reversal.",
            body:         [],
            previous:     $previous,
        );
    }

    /**
     * Devuelve el payload original para que el llamador construya el Reversal.
     *
     * @return array<string, mixed>
     */
    public function getReversalPayload(): array
    {
        return $this->payload;
    }
}
