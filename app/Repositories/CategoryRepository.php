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

    public function updateParentCategory(array $subIds, int $parentId = null): bool
    {
        return Category::whereIn('id', $subIds)->update(['parent_id' => $parentId]);
    }
}
