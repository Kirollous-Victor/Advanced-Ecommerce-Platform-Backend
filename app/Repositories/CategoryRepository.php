<?php

namespace App\Repositories;

use App\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoryRepository extends BaseEloquentRepository implements CategoryRepositoryInterface
{

    public function __construct(Category $category)
    {
        parent::__construct($category);
    }

    public function updateParentCategory(array $subIds, int $parentId): void
    {
        $subCategories = $this->getWhereIn('id', $subIds);
        DB::transaction(function () use ($subCategories, $parentId) {
            foreach ($subCategories as $subCategory) {
                $subCategory->update(['parent_id' => $parentId]);
            }
        });
    }
}
