<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransactionTraveler extends Model
{
    protected $fillable = [
        'payment_transaction_id',
        'position',
        'name',
        'email',
        'phone',
        'email_sent_at',
        'whatsapp_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'email_sent_at' => 'datetime',
            'whatsapp_sent_at' => 'datetime',
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(PaymentTransaction::class, 'payment_transaction_id');
    }
}
