<?php

namespace App\Repositories;

use App\Interfaces\CouponRepositoryInterface;
use App\Models\Coupon;

class CouponRepository extends BaseEloquentRepository implements CouponRepositoryInterface
{
    public function __construct(Coupon $category)
    {
        parent::__construct($category);
    }
}
