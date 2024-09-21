<?php

namespace App\Repositories;

use App\Interfaces\BaseQueryBuilderInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BaseQueryBuilderRepository implements BaseQueryBuilderInterface
{
    protected string $table;

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function all(array $columns = ['*'], array $orderBy = []): Collection
    {
        $query = DB::table($this->table)->select($columns);
        foreach ($orderBy as $column => $direction) {
            $query->orderBy($column, $direction);
        }
        return $query->get();
    }

    public function paginate(array $columns = ['*'], array $orderBy = [], int $paginate = 50,
                             array $andParameters = [], array $orParameters = []): LengthAwarePaginator
    {
        $query = DB::table($this->table)->select($columns)->where($andParameters)->orWhere($orParameters);
        foreach ($orderBy as $column => $direction) {
            $query->orderBy($column, $direction);
        }
        return $query->paginate($paginate);
    }

    public function getBy(array $andParameters, array $orParameters = [], array $columns = ['*'],
                          array $orderBy = []): Collection
    {
        $query = DB::table($this->table)->select($columns)->where($andParameters)->orWhere($orParameters);
        foreach ($orderBy as $column => $direction) {
            $query->orderBy($column, $direction);
        }
        return $query->get();
    }

    public function pluck(string $fieldName, string $fieldId = 'id'): Collection
    {
        return DB::table($this->table)->orderBy($fieldName)->pluck($fieldName, $fieldId);
    }

    public function pluckBy(string $listFieldName, string $listFieldId = 'id', array $andParameters = [],
                            array  $orParameters = []): Collection
    {
        return DB::table($this->table)->where($andParameters)->orWhere($orParameters)->orderBy($listFieldName)
            ->pluck($listFieldName, $listFieldId);
    }

    public function find(int $id, array $columns = ['*']): ?object
    {
        return DB::table($this->table)->select($columns)->find($id);
    }

    public function findBy(string $field, string $value, array $columns = ['*'], array $orderBy = []): ?object
    {
        $query = DB::table($this->table)->where($field, $value)->select($columns);
        foreach ($orderBy as $column => $direction) {
            $query->orderBy($column, $direction);
        }
        return $query->first();
    }

    public function findByMany(array $andParameters, array $orParameters = [], array $columns = ['*'],
                               array $orderBy = []): ?object
    {
        $query = DB::table($this->table)->select($columns)->where($andParameters)->orWhere($orParameters);
        foreach ($orderBy as $column => $direction) {
            $query->orderBy($column, $direction);
        }
        return $query->first();
    }

    public function getWhereIn(string $fieldName, array $values, array $columns = ['*'],
                               array  $orderBy = []): Collection
    {
        $query = DB::table($this->table)->select($columns)->whereIn($fieldName, $values);
        foreach ($orderBy as $column => $direction) {
            $query->orderBy($column, $direction);
        }
        return $query->get();
    }

    public function store(array $data): bool
    {
        return DB::table($this->table)->insert($data);
    }

    public function update(int $id, array $data): bool
    {
        return DB::table($this->table)->where('id', $id)->update($data);
    }

    public function updateBy(string $field, string $value, array $data): bool
    {
        return DB::table($this->table)->where($field, $value)->update($data);

    }

    public function updateByMany(array $andParameters, array $data, array $orParameters = []): bool
    {
        return DB::table($this->table)->where($andParameters)->orWhere($orParameters)->update($data);
    }

    public function destroy(int $id): bool
    {
        return DB::table($this->table)->where('id', $id)->delete();
    }

    public function destroyBy(string $field, string $value): bool
    {
        return DB::table($this->table)->where($field, $value)->delete();
    }

    public function destroyByMany(array $andParameters, array $orParameters = []): bool
    {
        return DB::table($this->table)->where($andParameters)->orWhere($orParameters)->delete();
    }

    public function isExists(int $id): bool
    {
        return DB::table($this->table)->where('id', $id)->exists();
    }

    public function isExistsBy(string $field, string $value): bool
    {
        return DB::table($this->table)->where($field, $value)->exists();
    }

    public function isExistsByMany(array $andParameters, array $orParameters = []): bool
    {
        return DB::table($this->table)->where($andParameters)->orWhere($orParameters)->exists();
    }

    public function count(array $andParameters = [], array $orParameters = []): int
    {
        return DB::table($this->table)->where($andParameters)->orWhere($orParameters)->count();
    }
}
