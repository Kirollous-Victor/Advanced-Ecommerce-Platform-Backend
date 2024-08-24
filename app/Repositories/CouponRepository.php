<?php

namespace App\Repositories;

use App\Interfaces\CouponRepositoryInterface;
use App\Models\Coupon;
use Illuminate\Database\Eloquent\Collection;

class CouponRepository extends BaseEloquentRepository implements CouponRepositoryInterface
{
    public function __construct(Coupon $coupon)
    {
        parent::__construct($coupon);
    }

    public function getAllUnexpired(array $columns = ['*'], array $orderBy = [], array $relations = []): Collection
    {
        return $this->getBy([['expiry_date', '>', now()]], ['expiry_date' => null], $columns, $orderBy, $relations);
    }

    public function getAllExpired(array $columns = ['*'], array $orderBy = [], array $relations = []): Collection
    {
        return $this->getBy([['expiry_date', '<=', now()]], [], $columns, $orderBy, $relations);
    }
}
