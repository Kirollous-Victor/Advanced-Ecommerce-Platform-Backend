<?php

namespace App\Repositories;

use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;

class ProductRepository extends BaseEloquentRepository implements ProductRepositoryInterface
{

    public function __construct(Product $product)
    {
        parent::__construct($product);
    }

    public function find(int $id, array $columns = ['*'], array $relations = []): ?Product
    {
        $product = parent::find($id, $columns, $relations);
        if ($product){
            return Product::fromModel($product);
        }
        return null;
    }
}
