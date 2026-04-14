<?php

namespace App\Mail;

use App\Models\PaymentTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StaffBookingPaidMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PaymentTransaction $transaction,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment received: '.$this->transaction->payable?->title.' — '.$this->transaction->reference,
            replyTo: [
                new Address($this->transaction->customer_email, $this->transaction->customer_name),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.staff-booking-paid',
        );
    }
}
