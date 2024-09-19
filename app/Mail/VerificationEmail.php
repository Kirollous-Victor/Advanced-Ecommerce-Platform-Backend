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

    protected string $mailTo;
    protected string $name;
    protected string $code;

    public function __construct(string $mailTo, string $name, string $code)
    {
        $this->mailTo = $mailTo;
        $this->name = $name;
        $this->code = $code;
    }


    public function envelope(): Envelope
    {
        return new Envelope(
            from: env('MAIL_FROM_ADDRESS'),
            to: $this->mailTo,
            subject: 'Advanced E-commerce Verification Email',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verification',
            with: [
                'name' => $this->name,
                'code' => $this->code
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
