<?php

namespace App\Interfaces;

interface CategoryRepositoryInterface extends BaseEloquentInterface
{
    public function updateParentCategory(array $subIds, int $parentId): bool;
}
