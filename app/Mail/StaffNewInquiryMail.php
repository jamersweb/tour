<?php

namespace App\Mail;

use App\Models\ExperienceInquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StaffNewInquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ExperienceInquiry $inquiry,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New lead: '.$this->inquiry->name.' — '.$this->inquiry->interest,
            replyTo: [
                new Address($this->inquiry->email, $this->inquiry->name),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.staff-new-inquiry',
        );
    }
}
