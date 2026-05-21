<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeniciaTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'payable_type',
        'payable_id',
        'type',
        'status',
        'order_id',
        'transaction_id',
        'authnum',
        'affiliation',
        'merchant',
        'amount',
        'tip',
        'response_code',
        'approved',
        'card_brand',
        'card_product',
        'card_last4',
        'card_first6',
        'issuer_bank',
        'acquirer_bank',
        'currency_id',
        'currency_description',
        'msi_payments',
        'msi_plan_type',
        'feenicia_response',
        'original_transaction_id',
    ];

    protected $casts = [
        'approved'          => 'boolean',
        'amount'            => 'decimal:2',
        'tip'               => 'decimal:2',
        'feenicia_response' => 'array',
    ];

    // ──────────────────────────────────────────────
    //  Constantes de tipo y estado
    // ──────────────────────────────────────────────

    const TYPE_ONE_STEP_SALE  = 'ONE_STEP_SALE';
    const TYPE_CASH_SALE      = 'CASH_SALE';
    const TYPE_RECURRING      = 'RECURRING';
    const TYPE_REFUND         = 'REFUND';
    const TYPE_CANCELLATION   = 'CANCELLATION';
    const TYPE_REVERSAL       = 'REVERSAL';

    const STATUS_APPROVED     = 'approved';
    const STATUS_REJECTED     = 'rejected';
    const STATUS_TIMEOUT      = 'timeout';
    const STATUS_REVERSED     = 'reversed';
    const STATUS_REFUNDED     = 'refunded';
    const STATUS_CANCELLED    = 'cancelled';

    // ──────────────────────────────────────────────
    //  Relaciones
    // ──────────────────────────────────────────────

    /**
     * Relación polimórfica con tu modelo de negocio
     * (Order, Payment, Subscription, etc.)
     */
    public function payable()
    {
        return $this->morphTo();
    }

    /**
     * Transacción original (en caso de refund/cancel/reversal)
     */
    public function originalTransaction(): BelongsTo
    {
        return $this->belongsTo(FeeniciaTransaction::class, 'original_transaction_id');
    }

    /**
     * Transacciones relacionadas (refunds, cancellations, reversals)
     */
    public function relatedTransactions(): HasMany
    {
        return $this->hasMany(FeeniciaTransaction::class, 'original_transaction_id');
    }

    // ──────────────────────────────────────────────
    //  Factory methods — construye desde respuesta de Feenicia
    // ──────────────────────────────────────────────

    /**
     * Crea un registro a partir de la respuesta exitosa de Feenicia.
     *
     * Uso:
     *   $tx = FeeniciaTransaction::fromFeeniciaResponse(
     *       type:     FeeniciaTransaction::TYPE_ONE_STEP_SALE,
     *       response: $result,
     *       extra:    ['affiliation' => $data->affiliation, 'amount' => $data->amount]
     *   );
     *
     * @param  string               $type      Constante TYPE_*
     * @param  array<string, mixed> $response  Respuesta de Feenicia
     * @param  array<string, mixed> $extra     Campos adicionales (affiliation, amount, etc.)
     * @return static
     */
    public static function fromFeeniciaResponse(
        string $type,
        array  $response,
        array  $extra = [],
    ): static {
        return static::create(array_merge([
            'type'                 => $type,
            'status'               => ($response['approved'] ?? false)
                                        ? self::STATUS_APPROVED
                                        : self::STATUS_REJECTED,
            'transaction_id'       => $response['transactionId'] ?? null,
            'authnum'              => $response['authnum'] ?? null,
            'merchant'             => $response['merchant']['id'] ?? config('feenicia.merchant'),
            'response_code'        => $response['responseCode'] ?? null,
            'approved'             => $response['approved'] ?? false,
            'card_brand'           => $response['card']['brand'] ?? null,
            'card_product'         => $response['card']['product'] ?? null,
            'card_last4'           => $response['card']['last4Digits'] ?? null,
            'card_first6'          => $response['card']['first6Digits'] ?? null,
            'issuer_bank'          => $response['issuerBank']['name'] ?? null,
            'acquirer_bank'        => $response['acquirerBank']['name'] ?? null,
            'currency_id'          => $response['currency']['id'] ?? null,
            'currency_description' => $response['currency']['description'] ?? null,
            'tip'                  => $response['tip'] ?? 0,
            'feenicia_response'    => $response,
        ], $extra));
    }

    // ──────────────────────────────────────────────
    //  Scopes
    // ──────────────────────────────────────────────

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeByTransactionId($query, string $transactionId)
    {
        return $query->where('transaction_id', $transactionId);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // ──────────────────────────────────────────────
    //  Helpers
    // ──────────────────────────────────────────────

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function hasBeenRefunded(): bool
    {
        return $this->relatedTransactions()
                    ->where('type', self::TYPE_REFUND)
                    ->where('status', self::STATUS_APPROVED)
                    ->exists();
    }

    public function hasBeenReversed(): bool
    {
        return $this->relatedTransactions()
                    ->where('type', self::TYPE_REVERSAL)
                    ->where('status', self::STATUS_APPROVED)
                    ->exists();
    }
}
