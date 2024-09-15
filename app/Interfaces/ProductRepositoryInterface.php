<?php

namespace App\Interfaces;

use App\Models\Product;

interface ProductRepositoryInterface extends SoftDeletingRepositoryInterface
{
    public function find(int $id, array $columns = ['*'], array $relations = []): ?Product;

    public function findTrash(int $id, array $columns = ['*'], array $relations = []): ?Product;

}
