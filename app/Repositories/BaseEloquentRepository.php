<?php

namespace App\Repositories;

use App\Interfaces\BaseEloquentInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class BaseEloquentRepository implements BaseEloquentInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(array $columns = ['*'], array $orderBy = [], array $relations = []): Collection
    {
        $query = $this->model::with($relations)->select($columns);
        foreach ($orderBy as $column => $direction) {
            $query->orderBy($column, $direction);
        }
        return $query->get();
    }

    public function paginate(array $columns = ['*'], array $orderBy = [], array $relations = [], int $paginate = 50,
                             array $parameters = []): LengthAwarePaginator
    {
        $query = $this->model::with($relations)->select($columns);
        foreach ($orderBy as $column => $direction) {
            $query->orderBy($column, $direction);
        }
        foreach ($parameters as $column => $value) {
            $query->where($column, $value);
        }
        return $query->paginate($paginate);
    }

    public function getBy(array $parameters, array $columns = ['*'], array $orderBy = [],
                          array $relations = []): Collection
    {
        $query = $this->model::with($relations)->select($columns)->where($parameters);
        foreach ($orderBy as $column => $direction) {
            $query->orderBy($column, $direction);
        }
        return $query->get();
    }

    public function pluck(string $fieldName, string $fieldId = 'id'): mixed
    {
        return $this->model::orderBy($fieldName)->pluck($fieldName, $fieldId);
    }

    public function pluckBy(array $parameters, string $listFieldName, string $listFieldId = 'id'): mixed
    {
        return $this->model::where($parameters)->orderBy($listFieldName)->pluck($listFieldName, $listFieldId);
    }

    public function find(int $id, array $columns = ['*'], array $relations = []): ?Model
    {
        return $this->model::with($relations)->select($columns)->find($id);
    }

    public function findBy(string $field, string $value, array $columns = ['*'], array $orderBy = [],
                           array  $relations = []): ?Model
    {
        $query = $this->model::with($relations)->where($field, $value)->select($columns);
        foreach ($orderBy as $column => $direction) {
            $query->orderBy($column, $direction);
        }
        return $query->first();
    }

    public function findByMany(array $parameters, array $columns = ['*'], array $orderBy = [],
                               array $relations = []): ?Model
    {
        $query = $this->model::with($relations)->select($columns)->where($parameters);
        foreach ($orderBy as $column => $direction) {
            $query->orderBy($column, $direction);
        }
        return $query->first();
    }

    public function getWhereIn(string $fieldName, array $values, array $columns = ['*'], array $orderBy = [],
                               array  $relations = []): Collection
    {
        $query = $this->model::with($relations)->select($columns)->whereIn($fieldName, $values);
        foreach ($orderBy as $column => $direction) {
            $query->orderBy($column, $direction);
        }
        return $query->get();
    }

    public function store(array $data): Model
    {
        return $this->model::create($data);
    }

    public function update(int $id, array $data): bool|Model
    {
        $query = $this->find($id);
        if ($query) {
            $query->update($data);
            return $query;
        }
        return false;
    }

    public function destroy($id): bool
    {
        $query = $this->find($id);
        if ($query) {
            return $query->delete();
        }
        return false;
    }

    public function count(array $parameters = []): int
    {
        return $this->model::where($parameters)->count();
    }
}
