<?php

namespace App\Services\Payments;

use App\Models\PaymentTransaction;
use App\Models\User;

class PaymentOperationsService
{
    public function __construct(
        protected BookingConfirmationService $confirmationService,
        protected PaymentTransactionLogger $activityLogger,
    ) {}

    public function reconcile(PaymentTransaction $transaction, User $user, string $status = 'paid', ?string $notes = null): PaymentTransaction
    {
        $previousStatus = $transaction->status;

        $attributes = [
            'status' => $status,
            'reconciled_at' => now(),
            'reconciled_by' => $user->id,
            'notes' => $this->appendNote($transaction->notes, $notes),
        ];

        if (in_array($status, ['paid', 'authorized'], true) && ! $transaction->paid_at) {
            $attributes['paid_at'] = now();
        }

        $transaction->update($attributes);

        $this->activityLogger->record(
            $transaction->fresh(),
            'manual_reconcile',
            'Payment reconciled from admin (manual).',
            [
                'target_status' => $status,
                'status_updated' => $previousStatus !== $status,
            ],
            $user,
        );

        if (in_array($status, ['paid', 'authorized'], true)) {
            $this->confirmationService->send($transaction->fresh());
        }

        return $transaction->fresh();
    }

    public function refund(PaymentTransaction $transaction, User $user, ?string $notes = null): PaymentTransaction
    {
        $previousStatus = $transaction->status;

        $transaction->update([
            'status' => 'refunded',
            'refunded_at' => now(),
            'refunded_by' => $user->id,
            'notes' => $this->appendNote($transaction->notes, $notes),
        ]);

        $this->activityLogger->record(
            $transaction->fresh(),
            'manual_refund',
            'Payment marked as refunded in admin.',
            [
                'note_excerpt' => $notes ? mb_substr((string) $notes, 0, 240) : null,
                'status_updated' => $previousStatus !== 'refunded',
            ],
            $user,
        );

        return $transaction->fresh();
    }

    public function resendConfirmation(PaymentTransaction $transaction): PaymentTransaction
    {
        $this->confirmationService->send($transaction->fresh(), true, false);

        return $transaction->fresh();
    }

    protected function appendNote(?string $existing, ?string $new): ?string
    {
        $new = trim((string) $new);

        if ($new === '') {
            return $existing;
        }

        return trim(implode("\n\n", array_filter([$existing, $new])));
    }
}
