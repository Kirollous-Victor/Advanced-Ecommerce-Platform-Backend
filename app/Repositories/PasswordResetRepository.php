<?php

namespace App\Repositories;

use App\Interfaces\PasswordResetRepositoryInterface;

class PasswordResetRepository extends BaseQueryBuilderRepository implements PasswordResetRepositoryInterface
{
    public function __construct()
    {
        parent::__construct('password_reset_tokens');
    }
}
