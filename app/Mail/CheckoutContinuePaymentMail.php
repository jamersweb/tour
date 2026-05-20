<?php

namespace App\Mail;

use App\Models\PaymentTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CheckoutContinuePaymentMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PaymentTransaction $transaction,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Complete your payment - '.$this->transaction->bookingTitle(),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.checkout-continue-payment',
        );
    }
}
