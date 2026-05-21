<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeniciaToken extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'token',
        'alias',
        'affiliation',
        'card_brand',
        'card_product',
        'card_last4',
        'card_first6',
        'cardholder_name',
        'exp_date',
        'status',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    // ──────────────────────────────────────────────
    //  Relaciones
    // ──────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(FeeniciaTransaction::class, 'token', 'token');
    }

    // ──────────────────────────────────────────────
    //  Helpers
    // ──────────────────────────────────────────────

    /**
     * Nombre para mostrar en la UI.
     * Ej: "Visa ···· 4242" o el alias si existe.
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

    // ──────────────────────────────────────────────
    //  Scopes
    // ──────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
