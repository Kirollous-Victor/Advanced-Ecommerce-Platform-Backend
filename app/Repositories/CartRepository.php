<?php

namespace App\Repositories;

use App\Interfaces\CartRepositoryInterface;
use App\Models\Cart;

class CartRepository extends BaseEloquentRepository implements CartRepositoryInterface
{
    public function __construct(Cart $cart)
    {
        parent::__construct($cart);
    }
}
