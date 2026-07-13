<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProsaTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'registration_id',
        'payment_id',
        'payment_type',
        'amount',
        'currency',
        'result_code',
        'result_description',
        'brand',
        'last4',
        'status',
        'origen',
        'raw_response',
    ];

    protected $casts = [
        'raw_response' => 'array',
        'amount'       => 'decimal:2',
    ];
}
