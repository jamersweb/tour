<?php

namespace App\Services\Payments;

use App\Mail\BookingConfirmedMail;
use App\Models\PaymentTransaction;
use App\Models\PaymentTransactionTraveler;
use App\Services\Messaging\WhatsappNotificationService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingConfirmationService
{
    public function __construct(
        protected WhatsappNotificationService $whatsapp,
    ) {
    }

    public function send(PaymentTransaction $transaction, bool $force = false): void
    {
        if (! $force && $transaction->confirmation_sent_at) {
            return;
        }

        $transaction->loadMissing('payable', 'travelers');
        $emailedRecipients = [];
        $whatsappRecipients = [];

        $this->sendEmailOnce(
            $emailedRecipients,
            $transaction->customer_email,
            new BookingConfirmedMail($transaction, $transaction->customer_name)
        );

        $travelerTimestamps = [];

        foreach ($transaction->travelers as $traveler) {
            $sentEmail = $this->sendEmailOnce(
                $emailedRecipients,
                $traveler->email,
                new BookingConfirmedMail($transaction, $traveler->name, $traveler)
            );

            if ($sentEmail) {
                $travelerTimestamps[$traveler->id]['email_sent_at'] = $sentEmail;
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

        $transaction->forceFill([
            'confirmation_sent_at' => now(),
        ])->save();
    }

    protected function sendEmailOnce(array &$emailedRecipients, string $email, BookingConfirmedMail $mailable): ?Carbon
    {
        $recipientKey = mb_strtolower(trim($email));

        if (isset($emailedRecipients[$recipientKey])) {
            return null;
        }

        Mail::to($email)->send($mailable);
        $emailedRecipients[$recipientKey] = true;

        return now();
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
