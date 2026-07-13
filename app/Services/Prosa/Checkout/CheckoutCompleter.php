<?php

namespace App\Services\Prosa\Checkout;

use App\Models\ProsaPendingCheckout;

/**
 * Completa la lógica de negocio de un flujo de checkout una vez que el pago
 * Prosa quedó aprobado (ya sea frictionless o tras el reto 3DS).
 *
 * Cada flujo (pagos, adquirir, solicitudes) implementa esta interfaz; el
 * {@see CheckoutManager} los despacha según `ProsaPendingCheckout::$flow`.
 */
interface CheckoutCompleter
{
    /** Identificador del flujo, p.ej. 'pagos', 'adquirir'. */
    public function flow(): string;

    /**
     * Ejecuta el pipeline post-pago y devuelve la URL de éxito a la que se
     * debe redirigir al cliente.
     *
     * @param  array<string, mixed>  $paymentResult  Resultado normalizado de PaymentService.
     */
    public function complete(ProsaPendingCheckout $checkout, array $paymentResult): string;

    /** URL a la que redirigir cuando el pago fue rechazado/cancelado. */
    public function failUrl(ProsaPendingCheckout $checkout, string $reason): string;
}
