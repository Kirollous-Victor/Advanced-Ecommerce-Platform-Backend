<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Collection;

interface CouponRepositoryInterface extends BaseEloquentInterface
{
    public function getAllUnexpired(array $columns = ['*'], array $orderBy = [], array $relations = []): Collection;

    public function getAllExpired(array $columns = ['*'], array $orderBy = [], array $relations = []): Collection;

}
