<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected array $mailData;

    public function __construct(array $mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: env('MAIL_FROM_ADDRESS'),
            to: $this->mailData['email'],
            subject: 'Advanced E-commerce Verification Email',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verification',
            with: [
                'name' => $this->mailData['name'],
                'code' => $this->mailData['code']
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
