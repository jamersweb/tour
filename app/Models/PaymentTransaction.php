<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'payable_type',
        'payable_id',
        'user_id',
        'gateway',
        'reference',
        'status',
        'customer_name',
        'customer_email',
        'customer_phone',
        'travel_date',
        'guest_count',
        'amount',
        'amount_minor',
        'currency',
        'gateway_order_ref',
        'gateway_payment_ref',
        'payment_url',
        'gateway_payload',
        'paid_at',
        'confirmation_sent_at',
        'reconciled_at',
        'reconciled_by',
        'refunded_at',
        'refunded_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'travel_date' => 'date',
            'guest_count' => 'integer',
            'amount' => 'decimal:2',
            'amount_minor' => 'integer',
            'gateway_payload' => 'array',
            'paid_at' => 'datetime',
            'confirmation_sent_at' => 'datetime',
            'reconciled_at' => 'datetime',
            'refunded_at' => 'datetime',
        ];
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function travelers(): HasMany
    {
        return $this->hasMany(PaymentTransactionTraveler::class)->orderBy('position');
    }

    public function reconciledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reconciled_by');
    }

    public function refundedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'refunded_by');
    }
}
