<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseEloquentInterface
{
    public function all(array $columns = ['*'], array $orderBy = [], array $relations = []): Collection;

    public function paginate(array $columns = ['*'], array $orderBy = [], array $relations = [], int $paginate = 50,
                             array $andParameters = [], array $orParameters = []): LengthAwarePaginator;

    public function getBy(array $andParameters, array $orParameters = [], array $columns = ['*'], array $orderBy = [],
                          array $relations = []): Collection;

    public function pluck(string $fieldName, string $fieldId = 'id'): Collection;

    public function pluckBy(string $listFieldName, string $listFieldId = 'id', array $andParameters = [],
                            array  $orParameters = []): Collection;

    public function find(int $id, array $columns = ['*'], array $relations = []): ?Model;

    public function findBy(string $field, string $value, array $columns = ['*'], array $orderBy = [],
                           array  $relations = []): ?Model;

    public function findByMany(array $andParameters, array $orParameters = [], array $columns = ['*'],
                               array $orderBy = [], array $relations = []): ?Model;

    public function getWhereIn(string $fieldName, array $values, array $columns = ['*'], array $orderBy = [],
                               array  $relations = []): Collection;

    public function store(array $data): Model;

    public function update(int $id, array $data): bool|Model;

    public function updateBy(string $field, string $value, array $data): bool;

    public function updateByMany(array $andParameters, array $data, array $orParameters = []): bool;

    public function destroy(int $id): bool;

    public function isExists(int $id): bool;

    public function isExistsBy(string $field, string $value): bool;

    public function isExistsByMany(array $andParameters, array $orParameters = []): bool;

    public function count(array $andParameters = [], array $orParameters = []): int;
}
