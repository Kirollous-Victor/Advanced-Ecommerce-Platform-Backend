<?php

namespace App\Repositories;

use App\Interfaces\EmailVerificationInterface;

class EmailVerificationRepository extends BaseQueryBuilderRepository implements EmailVerificationInterface
{
    public function __construct()
    {
        parent::__construct('email_verification');
    }
}
