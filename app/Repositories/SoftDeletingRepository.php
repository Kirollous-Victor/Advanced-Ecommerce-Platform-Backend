<?php

namespace App\Repositories;

use App\Interfaces\SoftDeletingRepositoryInterface;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class SoftDeletingRepository extends BaseEloquentRepository implements SoftDeletingRepositoryInterface
{
    protected Product $product;

    public function __construct(Model $category)
    {
        parent::__construct($category);
    }

    public function restore(int $id): bool
    {
        return $this->model::withTrashed()->where('id', $id)->restore();
    }

    public function onlyTrashed(array $columns = ['*'], array $andParameters = [], array $orParameters = [],
                                array $orderBy = [], array $relations = []): Collection
    {
        $query = $this->model::onlyTrashed()->with($relations)->select($columns)->where($andParameters)
            ->orWhere($orParameters);
        foreach ($orderBy as $column => $direction) {
            $query->orderBy($column, $direction);
        }
        return $query->get();
    }

    public function withTrashed(array $columns = ['*'], array $andParameters = [], array $orParameters = [],
                                array $orderBy = [], array $relations = []): Collection
    {
        $query = $this->model::withTrashed()->with($relations)->select($columns)->where($andParameters)
            ->orWhere($orParameters);
        foreach ($orderBy as $column => $direction) {
            $query->orderBy($column, $direction);
        }
        return $query->get();
    }

    public function findTrash(int $id, array $columns = ['*'], array $relations = []): ?Model
    {
        return $this->model::onlyTrashed()->with($relations)->select($columns)->find($id);
    }

    public function updateTrash(int $id, array $data): bool|Model
    {
        $query = $this->findTrash($id);
        if ($query) {
            $query->update($data);
            return $query;
        }
        return false;
    }

    public function forceDelete(int $id): bool
    {
        $query = $this->findTrash($id);
        if ($query) {
            return $query->forceDelete();
        }
        return false;
    }
}
