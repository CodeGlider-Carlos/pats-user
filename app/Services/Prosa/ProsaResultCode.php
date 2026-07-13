<?php

namespace App\Services\Prosa;

/**
 * Clasifica el `result.code` que devuelve OPPWA (patrón XXX.XXX.XXX).
 *
 * Las expresiones regulares provienen de la documentación de OPPWA/ACI:
 * https://prosa.docs.oppwa.com/reference/resultCodes
 */
class ProsaResultCode
{
    /** Procesado correctamente (autorizado y capturado). */
    private const SUCCESS = '/^(000\.000\.|000\.100\.1|000\.[36]|000\.400\.[1][12]0)/';

    /** Éxito pero marcado para revisión manual. */
    private const MANUAL_REVIEW = '/^(000\.400\.0[^3]|000\.400\.100)/';

    /** Pendiente (sesión abierta o confirmación diferida de sistemas externos). */
    private const PENDING = '/^(000\.200|800\.400\.5|100\.400\.500)/';

    public static function isSuccess(string $code): bool
    {
        return (bool) preg_match(self::SUCCESS, $code);
    }

    public static function isManualReview(string $code): bool
    {
        return (bool) preg_match(self::MANUAL_REVIEW, $code);
    }

    public static function isPending(string $code): bool
    {
        return (bool) preg_match(self::PENDING, $code);
    }

    /**
     * Pendiente por sesión abierta (000.200.*): típico de un reto 3DS en curso.
     * Cuando viene acompañado de un objeto `redirect`, el cliente debe ser
     * enviado al ACS del emisor.
     */
    public static function isChallengePending(string $code): bool
    {
        return (bool) preg_match('/^000\.200/', $code);
    }

    /**
     * Se considera "aprobado" si está procesado correctamente o en revisión
     * manual (la transacción existe y los fondos fueron capturados).
     */
    public static function isApproved(string $code): bool
    {
        return self::isSuccess($code) || self::isManualReview($code);
    }

    public static function isRejected(string $code): bool
    {
        return ! self::isApproved($code) && ! self::isPending($code);
    }
}
