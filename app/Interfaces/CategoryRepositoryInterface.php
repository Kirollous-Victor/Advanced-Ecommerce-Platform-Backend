<?php

namespace App\Interfaces;

interface CategoryRepositoryInterface extends BaseEloquentInterface
{
    public function updateParentCategory(int $subId, int $parentId): void;
}
