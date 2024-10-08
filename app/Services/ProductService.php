<?php

namespace App\Services;

use App\Interfaces\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ProductService
{
    private ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
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
    public function find(int $id, int $mode = null): ?Model
    {
        $columns = ['id', 'name', 'description', 'price', 'stock', 'vendor_id', 'category_id'];
        $relations = ['vendor'];
        switch ($mode) {
            case 1:
                return $this->productRepository->find($id, $columns, $relations);
            case 2:
                return $this->productRepository->findTrash($id, $columns, $relations);
            default:
                return $this->productRepository->find($id);
        }
    }

    public function all(int $mode = null): Collection
    {
        switch ($mode) {
            case 1:
                return $this->productRepository->all();
            case 2:
                return $this->productRepository->onlyTrashed();
            default:
                return $this->productRepository->withTrashed();
        }
    }

    public function store(array $productData): Model
    {
        return $this->productRepository->store($productData);
    }

    public function update(int $id, array $productData): bool|Model
    {
        return $this->productRepository->update($id, $productData);
    }

    public function destroy(int $id): bool
    {
        return $this->productRepository->destroy($id);
    }
}
