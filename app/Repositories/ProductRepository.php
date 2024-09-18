<?php

namespace App\Repositories;

use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;

class ProductRepository extends SoftDeletingRepository implements ProductRepositoryInterface
{

    public function __construct(Product $product)
    {
        parent::__construct($product);
    }
}
