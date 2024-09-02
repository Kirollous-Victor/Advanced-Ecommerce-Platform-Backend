<?php

namespace App\Interfaces;

use App\Repositories\SoftDeletingRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface SoftDeletingRepositoryInterface extends BaseEloquentInterface
{
    public function restore(int $id): bool;

    public function onlyTrashed(array $columns = ['*'], array $andParameters = [], array $orParameters = [],
                                array $orderBy = [], array $relations = []): Collection;

    public function findTrash(int $id, array $columns = ['*'], array $relations = []): ?Model;

    public function withTrashed(array $columns = ['*'], array $andParameters = [], array $orParameters = [],
                                array $orderBy = [], array $relations = []): Collection;

    public function updateTrash(int $id, array $data): bool|Model;

    public function forceDelete(int $id): bool;
}
