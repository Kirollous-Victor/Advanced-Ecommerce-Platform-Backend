<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;

class UserRepository extends SoftDeletingRepository implements UserRepositoryInterface
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    public function store(array $data): User
    {
        $user = parent::store($data);
        return User::fromModel($user);
    }
}
