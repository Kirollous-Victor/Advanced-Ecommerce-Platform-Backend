<?php

namespace App\Services;

use App\Interfaces\CouponRepositoryInterface;
use App\Models\Coupon;
use Illuminate\Database\Eloquent\Collection;

class CouponService
{
    private CouponRepositoryInterface $couponRepository;

    public function __construct(CouponRepositoryInterface $couponRepository)
    {
        $this->couponRepository = $couponRepository;
    }

    public function all(): Collection
    {
        return $this->couponRepository->all(['code', 'discount_type', 'discount_value', 'expiry_date'],
            ['expiry_date' => 'asc']);
    }

    public function store(array $couponData): Coupon
    {
        return Coupon::fromModel($this->couponRepository->store($couponData));
    }

    public function find(int $id): Coupon
    {
        return Coupon::fromModel($this->couponRepository->find($id));
    }

    public function update(int $id, array $couponData): bool|Coupon
    {
        $coupon = $this->couponRepository->update($id, $couponData);
        if ($coupon)
            return Coupon::fromModel($coupon);
        return false;
    }

    public function destroy(int $id): bool
    {
        return $this->couponRepository->destroy($id);
    }

}
