<?php

namespace App\Services;

use App\Interfaces\CouponRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

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

    public function store(array $couponData): Model
    {
        return $this->couponRepository->store($couponData);
    }

    public function find(int $id): Model
    {
        return $this->couponRepository->find($id);
    }

    public function update(int $id, array $couponData): bool|Model
    {
        return $this->couponRepository->update($id, $couponData);
    }

    public function destroy(int $id): bool
    {
        return $this->couponRepository->destroy($id);
    }

}
