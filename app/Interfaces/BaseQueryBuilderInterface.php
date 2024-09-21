<?php

namespace App\Interfaces;

use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseQueryBuilderInterface
{
    public function all(array $columns = ['*'], array $orderBy = []);

    public function paginate(array $columns = ['*'], array $orderBy = [], int $paginate = 50,
                             array $andParameters = [], array $orParameters = []): LengthAwarePaginator;

    public function getBy(array $andParameters, array $orParameters = [], array $columns = ['*'],
                          array $orderBy = []): Collection;

    public function pluck(string $fieldName, string $fieldId = 'id'): Collection;

    public function pluckBy(string $listFieldName, string $listFieldId = 'id', array $andParameters = [],
                            array  $orParameters = []): Collection;

    public function find(int $id, array $columns = ['*']): ?object;

    public function findBy(string $field, string $value, array $columns = ['*'], array $orderBy = []): ?object;

    public function findByMany(array $andParameters, array $orParameters = [], array $columns = ['*'],
                               array $orderBy = []): ?object;

    public function getWhereIn(string $fieldName, array $values, array $columns = ['*'],
                               array  $orderBy = []): Collection;

    public function store(array $data): bool;

    public function update(int $id, array $data): bool;

    public function updateBy(string $field, string $value, array $data): bool;

    public function updateByMany(array $andParameters, array $data, array $orParameters = []): bool;

    public function destroy(int $id): bool;

    public function destroyBy(string $field, string $value): bool;

    public function destroyByMany(array $andParameters, array $orParameters = []): bool;

    public function isExists(int $id): bool;

    public function isExistsBy(string $field, string $value): bool;

    public function isExistsByMany(array $andParameters, array $orParameters = []): bool;

    public function count(array $andParameters = [], array $orParameters = []): int;
}
