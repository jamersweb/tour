<?php

namespace App\Services\Payments;

use App\Models\PaymentTransaction;
use App\Models\PaymentTransactionLog;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PaymentTransactionLogger
{
    /**
     * Append an audit row for a payment / booking lifecycle event.
     *
     * @param  array<string, mixed>  $properties
     */
    public function record(
        PaymentTransaction $transaction,
        string $action,
        ?string $description = null,
        array $properties = [],
        ?User $causer = null,
    ): void {
        try {
            PaymentTransactionLog::query()->create([
                'payment_transaction_id' => $transaction->id,
                'user_id' => $causer?->id,
                'action' => $action,
                'description' => $description,
                'properties' => $properties === [] ? null : $properties,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('payment_transaction_log_failed', [
                'transaction_id' => $transaction->id,
                'action' => $action,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
