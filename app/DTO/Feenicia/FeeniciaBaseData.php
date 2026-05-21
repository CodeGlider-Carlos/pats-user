<?php

namespace App\DTO\Feenicia;

/**
 * Clase base para todos los DTOs de Feenicia.
 * Provee el método toArray() que usan los services para construir el payload.
 */
abstract class FeeniciaBaseData
{
    /**
     * Convierte el DTO a array listo para enviar como JSON a Feenicia.
     * Los campos nulos se excluyen automáticamente.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $result = [];
        foreach (get_object_vars($this) as $key => $value) {
            $cleanKey = trim($key);
            if ($value !== null) {
                $result[$cleanKey] = is_string($value) ? trim($value) : $value;
            }
        }
        return $result;
    }
}
