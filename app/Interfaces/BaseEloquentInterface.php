<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseEloquentInterface
{
    public function all(array $columns = ['*'], array $orderBy = [], array $relations = []): Collection;

    public function paginate(array $columns = ['*'], array $orderBy = [], array $relations = [], int $paginate = 50,
                             array $parameters = []): LengthAwarePaginator;

    public function getBy(array $parameters, array $columns = ['*'], array $orderBy = [],
                          array $relations = []): Collection;

    public function pluck(string $fieldName, string $fieldId = 'id'): mixed;

    public function pluckBy(array $parameters, string $listFieldName, string $listFieldId = 'id'): mixed;

    public function find(int $id, array $columns = ['*'], array $relations = []): ?Model;

    public function findBy(string $field, string $value, array $columns = ['*'], array $orderBy = [],
                           array  $relations = []): ?Model;

    public function findByMany(array $parameters, array $columns = ['*'], array $orderBy = [],
                               array $relations = []): ?Model;

    public function getWhereIn(string $fieldName, array $values, array $columns = ['*'], array $orderBy = [],
                               array  $relations = []): Collection;

    public function store(array $data): Model;

    public function update(int $id, array $data): bool|Model;

    public function destroy($id): bool;

    public function count(array $parameters = []): int;
}
