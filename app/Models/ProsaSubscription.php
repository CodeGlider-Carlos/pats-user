<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProsaSubscription extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'correo_usuario',
        'registration_id',
        'card_brand',
        'card_last4',
        'frecuencia',
        'meses',
        'id_tipo_precio',
        'amount',
        'currency',
        'status',
        'failure_count',
        'last_charged_at',
        'next_charge_at',
    ];

    protected $casts = [
        'amount'          => 'decimal:2',
        'last_charged_at' => 'datetime',
        'next_charge_at'  => 'datetime',
    ];

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isDue(): bool
    {
        return $this->isActive()
            && $this->next_charge_at !== null
            && $this->next_charge_at->isPast();
    }

    /** Calcula la próxima fecha de cobro a partir de ahora. */
    public function calculateNextChargeAt(): Carbon
    {
        $base = Carbon::now();

        return $this->frecuencia === 'ANUAL'
            ? $base->addYear()
            : $base->addMonths($this->meses);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeDue(Builder $query): Builder
    {
        return $query->active()->where('next_charge_at', '<=', now());
    }
}
