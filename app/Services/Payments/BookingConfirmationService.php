<?php

namespace App\Services\Payments;

use App\Mail\BookingConfirmedMail;
use App\Models\PaymentTransaction;
use App\Models\PaymentTransactionTraveler;
use App\Services\AdminBookingNotifier;
use App\Services\Messaging\WhatsappNotificationService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingConfirmationService
{
    public function __construct(
        protected WhatsappNotificationService $whatsapp,
        protected AdminBookingNotifier $adminNotifier,
        protected PaymentTransactionLogger $activityLogger,
    ) {}

    public function send(PaymentTransaction $transaction, bool $force = false, bool $notifyStaff = true): void
    {
        if (! $force && $transaction->confirmation_sent_at) {
            return;
        }

        $transaction->loadMissing('payable', 'travelers');
        $whatsappRecipients = [];
        $emailFailures = [];

        try {
            Mail::to($transaction->customer_email)->send(
                new BookingConfirmedMail($transaction, $transaction->customer_name)
            );
        } catch (\Throwable $exception) {
            $emailFailures[] = [
                'recipient' => $transaction->customer_email,
                'type' => 'customer',
                'message' => $exception->getMessage(),
            ];

            Log::warning('Booking confirmation email to customer failed.', [
                'transaction_id' => $transaction->id,
                'message' => $exception->getMessage(),
            ]);
        }

        $travelerTimestamps = [];
        $travelerEmailSentAt = [];

        foreach ($transaction->travelers as $traveler) {
            $key = mb_strtolower(trim((string) $traveler->email));

            if ($key === '') {
                continue;
            }

            if (isset($travelerEmailSentAt[$key])) {
                $travelerTimestamps[$traveler->id]['email_sent_at'] = $travelerEmailSentAt[$key];

                continue;
            }

            try {
                Mail::to($traveler->email)->send(
                    new BookingConfirmedMail($transaction, $traveler->name, $traveler)
                );
                $travelerEmailSentAt[$key] = now();
                $travelerTimestamps[$traveler->id]['email_sent_at'] = $travelerEmailSentAt[$key];
            } catch (\Throwable $exception) {
                $emailFailures[] = [
                    'recipient' => $traveler->email,
                    'type' => 'traveler',
                    'traveler_id' => $traveler->id,
                    'message' => $exception->getMessage(),
                ];

                Log::warning('Booking confirmation email to traveler failed.', [
                    'transaction_id' => $transaction->id,
                    'traveler_id' => $traveler->id,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        $this->sendWhatsappSafely(
            $whatsappRecipients,
            $transaction,
            $transaction->customer_name,
            $transaction->customer_phone
        );

        foreach ($transaction->travelers as $traveler) {
            $sentAt = $this->sendWhatsappSafely(
                $whatsappRecipients,
                $transaction,
                $traveler->name,
                $traveler->phone
            );

            if ($sentAt) {
                $travelerTimestamps[$traveler->id]['whatsapp_sent_at'] = $sentAt;
            }
        }

        foreach ($travelerTimestamps as $travelerId => $attributes) {
            PaymentTransactionTraveler::query()->whereKey($travelerId)->update($attributes);
        }

        if ($notifyStaff) {
            $this->adminNotifier->paymentReceived($transaction);
        }

        if ($emailFailures === []) {
            $transaction->forceFill([
                'confirmation_sent_at' => now(),
            ])->save();
        }

        $this->activityLogger->record(
            $transaction->fresh(),
            $emailFailures === []
                ? ($force ? 'booking_confirmation_resent' : 'booking_confirmation_sent')
                : 'booking_confirmation_email_failed',
            $emailFailures === []
                ? ($force
                    ? 'Booking confirmation emails resent to booker and travelers (staff copies skipped if configured).'
                    : 'Booking confirmation emails sent to booker and travelers; staff notifications dispatched when enabled.')
                : 'One or more booking confirmation emails failed; confirmation remains pending for retry.',
            [
                'notify_staff' => $notifyStaff,
                'email_failures' => $emailFailures,
            ],
            auth()->user(),
        );
    }

    protected function sendWhatsappSafely(array &$whatsappRecipients, PaymentTransaction $transaction, string $name, ?string $phone): ?Carbon
    {
        if (! $phone) {
            return null;
        }

        $recipientKey = preg_replace('/\s+/', '', $phone) ?: $phone;

        if (isset($whatsappRecipients[$recipientKey])) {
            return null;
        }

        try {
            $this->whatsapp->sendBookingConfirmation($transaction, $name, $phone);
            $whatsappRecipients[$recipientKey] = true;

            return now();
        } catch (\Throwable $exception) {
            Log::warning('WhatsApp booking confirmation skipped.', [
                'transaction_id' => $transaction->id,
                'phone' => $phone,
                'message' => $exception->getMessage(),
            ]);

            return null;
        }
    }
}
