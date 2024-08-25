<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;

class ProductService
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * 1 => columns: 'id', 'name', 'description', 'price', 'stock', 'vendor_id', 'category_id', 'deleted_at'
     * relations: 'vendor'
     *
     * default => Full Product Model
     * @param int|null $mode 1, 2
     */
    public function find(int $id, int $mode = null): ?Product
    {
        switch ($mode) {
            case 1:
                return $this->productRepository->find($id, ['id', 'name', 'description', 'price', 'stock',
                    'vendor_id', 'category_id', 'deleted_at'], ['vendor']);
            default:
                return $this->productRepository->find($id);
        }
    }
}
