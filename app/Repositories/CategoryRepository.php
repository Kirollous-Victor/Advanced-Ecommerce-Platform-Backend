<?php

namespace App\Repositories;

use App\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;

class CategoryRepository extends BaseEloquentRepository implements CategoryRepositoryInterface
{

    public function __construct(Category $category)
    {
        parent::__construct($category);
    }

    public function updateParentCategory(int $subId, int $parentId): void
    {
        $subCategory = $this->find($subId);
        $subCategory->update(['parent_id' => $parentId]);
    }
}
