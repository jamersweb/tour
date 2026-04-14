<?php

namespace App\Observers;

use App\Models\PaymentTransaction;
use App\Services\Payments\PaymentTransactionLogger;

class PaymentTransactionObserver
{
    public function updated(PaymentTransaction $transaction): void
    {
        if (! $transaction->wasChanged('status')) {
            return;
        }

        $from = $transaction->getOriginal('status');
        $to = $transaction->status;

        app(PaymentTransactionLogger::class)->record(
            $transaction,
            'status_changed',
            "Status changed from {$from} to {$to}.",
            [
                'from' => $from,
                'to' => $to,
            ],
            auth()->user(),
        );
    }
}
