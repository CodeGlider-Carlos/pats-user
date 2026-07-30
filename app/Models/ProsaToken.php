<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProsaToken extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'registration_id',
        'alias',
        'card_brand',
        'card_bin',
        'card_last4',
        'cardholder_name',
        'exp_month',
        'exp_year',
        'status',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Nombre para mostrar en la UI. Ej: "Visa ···· 4242" o el alias si existe.
     */
    public function displayName(): string
    {
        if ($this->alias) {
            return $this->alias;
        }

        $brand = $this->card_brand ?? 'Tarjeta';
        $last4 = $this->card_last4 ? '···· ' . $this->card_last4 : '';

        return trim("{$brand} {$last4}");
    }

    public function expDate(): string
    {
        if ($this->exp_month && $this->exp_year) {
            return $this->exp_month . '/' . substr($this->exp_year, -2);
        }

        return '—';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Marca esta tarjeta como default y desmarca las demás del mismo usuario.
     */
    public function setAsDefault(): void
    {
        static::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }
}
