<?php

namespace App\Services;

use App\Mail\VerificationEmail;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    public function sendVerificationEmail(string $email, string $userName, string $code): void
    {
        Mail::to($email)->send(new VerificationEmail($email, $userName, $code));
    }

}
