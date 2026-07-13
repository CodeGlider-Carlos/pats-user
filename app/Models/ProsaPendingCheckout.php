<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Contexto de un checkout en curso que pudo derivar en un reto 3DS.
 *
 * Se persiste antes de redirigir al cliente al ACS para poder completar la
 * lógica de negocio (pasaporte, solicitud, comisiones) cuando regresa.
 */
class ProsaPendingCheckout extends Model
{
    public const STATUS_PENDING   = 'pending';
    public const STATUS_CHALLENGE = 'challenge';
    public const STATUS_APPROVED  = 'approved';
    public const STATUS_DECLINED  = 'declined';
    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'merchant_transaction_id',
        'payment_id',
        'flow',
        'status',
        'user_id',
        'amount',
        'payload',
        'redirect',
        'result_code',
        'result_description',
    ];

    protected $casts = [
        'payload'  => 'array',
        'redirect' => 'array',
        'amount'   => 'decimal:2',
    ];
}
