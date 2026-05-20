<?php

namespace App\Mail;

use App\Models\PaymentTransaction;
use App\Models\PaymentTransactionTraveler;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PaymentTransaction $transaction,
        public string $recipientName,
        public ?PaymentTransactionTraveler $traveler = null,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking confirmed: '.$this->transaction->bookingTitle(),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-confirmed',
        );
    }
}
