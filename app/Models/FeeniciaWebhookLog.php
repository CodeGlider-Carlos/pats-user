<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeniciaWebhookLog extends Model
{
    protected $fillable = [
        'tipo_tx',
        'merchant',
        'feenicia_id',
        'payload',
        'jwt_valid',
        'processed',
        'ip',
    ];

    protected $casts = [
        'payload'   => 'array',
        'jwt_valid' => 'boolean',
        'processed' => 'boolean',
    ];
}
