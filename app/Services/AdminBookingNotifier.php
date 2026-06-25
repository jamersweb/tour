<?php

namespace App\Services;

use App\Mail\StaffBookingPaidMail;
use App\Mail\StaffNewInquiryMail;
use App\Mail\StaffNewPaymentTransactionMail;
use App\Models\ExperienceInquiry;
use App\Models\PaymentTransaction;
use App\Models\User;
use App\Support\BookingMailRecipient;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Sends staff-facing mailables to the operations inbox and, when enabled, admin users,
 * deduplicating by email address so the same inbox is not emailed twice.
 * Also writes Filament database notifications for admin users (panel bell).
 */
class AdminBookingNotifier
{
    /**
     * @param  callable(): Mailable  $mailableFactory
     * @param  array<int, string|null>  $excludedEmails
     */
    protected function mailOperationsAndAdmins(callable $mailableFactory, array $excludedEmails = []): void
    {
        $sentTo = [];
        $excluded = $this->normalizeEmailSet($excludedEmails);

        $operations = BookingMailRecipient::operationsEmail();
        if ($operations && ! isset($excluded[mb_strtolower(trim($operations))])) {
            try {
                Mail::to($operations)->send($mailableFactory());
                $sentTo[mb_strtolower(trim($operations))] = true;
            } catch (\Throwable $exception) {
                Log::warning('Operations booking email failed.', [
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        if ((bool) config('mail.bookings.notify_admin_users', false)) {
            foreach (User::query()->where('is_admin', true)->cursor() as $admin) {
                $key = mb_strtolower(trim((string) $admin->email));
                if ($key === '' || isset($sentTo[$key]) || isset($excluded[$key])) {
                    continue;
                }

                try {
                    Mail::to($admin->email)->send($mailableFactory());
                    $sentTo[$key] = true;
                } catch (\Throwable $exception) {
                    Log::warning('Admin booking email failed.', [
                        'admin_id' => $admin->id,
                        'message' => $exception->getMessage(),
                    ]);
                }
            }
        }
    }

    /**
     * @param  array<int, string|null>  $emails
     * @return array<string, true>
     */
    protected function normalizeEmailSet(array $emails): array
    {
        $normalized = [];

        foreach ($emails as $email) {
            $key = mb_strtolower(trim((string) $email));

            if ($key !== '') {
                $normalized[$key] = true;
            }
        }

        return $normalized;
    }

    protected function notifyAdminsInPanel(Notification $notification): void
    {
        $admins = User::query()->where('is_admin', true)->get();

        if ($admins->isEmpty()) {
            return;
        }

        try {
            $notification->sendToDatabase($admins);
        } catch (\Throwable $exception) {
            Log::warning('Admin panel notification failed.', [
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function inquiryCreated(ExperienceInquiry $inquiry): void
    {
        $this->mailOperationsAndAdmins(
            fn () => new StaffNewInquiryMail($inquiry),
            [$inquiry->email],
        );

        $this->notifyAdminsInPanel(
            Notification::make()
                ->title('New inquiry')
                ->body("{$inquiry->name} — {$inquiry->interest}")
                ->icon('heroicon-o-chat-bubble-left-right')
                ->actions([
                    Action::make('view')
                        ->label('Open in admin')
                        ->url(url('/admin/experience-inquiries/'.$inquiry->id)),
                ])
        );
    }

    public function checkoutPending(PaymentTransaction $transaction): void
    {
        $transaction->loadMissing('payable', 'travelers');

        $this->mailOperationsAndAdmins(
            fn () => new StaffNewPaymentTransactionMail($transaction),
            $this->customerEmails($transaction),
        );

        $this->notifyAdminsInPanel(
            Notification::make()
                ->title('Checkout started')
                ->body("{$transaction->customer_name} - ".$transaction->bookingTitle()." ({$transaction->reference})")
                ->icon('heroicon-o-credit-card')
                ->actions([
                    Action::make('view')
                        ->label('View transaction')
                        ->url(url('/admin/payment-transactions/'.$transaction->id)),
                ])
        );
    }

    public function paymentReceived(PaymentTransaction $transaction): void
    {
        $transaction->loadMissing('payable', 'travelers');

        $this->mailOperationsAndAdmins(
            fn () => new StaffBookingPaidMail($transaction->fresh(['payable', 'travelers'])),
            $this->customerEmails($transaction),
        );

        $this->notifyAdminsInPanel(
            Notification::make()
                ->title('Payment received')
                ->body($transaction->bookingTitle()." - {$transaction->reference}")
                ->icon('heroicon-o-check-circle')
                ->actions([
                    Action::make('view')
                        ->label('View transaction')
                        ->url(url('/admin/payment-transactions/'.$transaction->id)),
                ])
        );
    }

    /**
     * @return array<int, string|null>
     */
    protected function customerEmails(PaymentTransaction $transaction): array
    {
        return [
            $transaction->customer_email,
            ...$transaction->travelers->pluck('email')->all(),
        ];
    }
}
