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
 * Sends staff-facing mailables to the operations inbox plus each user with {@see User::$is_admin},
 * deduplicating by email address so the same inbox is not emailed twice.
 * Also writes Filament database notifications for admin users (panel bell).
 */
class AdminBookingNotifier
{
    /**
     * @param  callable(): Mailable  $mailableFactory
     */
    protected function mailOperationsAndAdmins(callable $mailableFactory): void
    {
        $sentTo = [];

        $operations = BookingMailRecipient::operationsEmail();
        if ($operations) {
            try {
                Mail::to($operations)->send($mailableFactory());
                $sentTo[mb_strtolower(trim($operations))] = true;
            } catch (\Throwable $exception) {
                Log::warning('Operations booking email failed.', [
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        foreach (User::query()->where('is_admin', true)->cursor() as $admin) {
            $key = mb_strtolower(trim((string) $admin->email));
            if ($key === '' || isset($sentTo[$key])) {
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
        $this->mailOperationsAndAdmins(fn () => new StaffNewInquiryMail($inquiry));

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
        $transaction->loadMissing('payable');

        $this->mailOperationsAndAdmins(fn () => new StaffNewPaymentTransactionMail($transaction));

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
        $transaction->loadMissing('payable');

        $this->mailOperationsAndAdmins(fn () => new StaffBookingPaidMail($transaction->fresh(['payable'])));

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
}
