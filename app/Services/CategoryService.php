<?php

namespace App\Services;

use App\Repositories\CategoryRepository;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    private CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getCategories(int $mode = null): Collection
    {
        switch ($mode) {
            case 1:
            {
                return $this->categoryRepository->getBy(['parent_id' => null], ['id', 'name'], ['name' => 'asc'],
                    ['subCategories' => function (Builder $query) {
                        $query->with(['subCategories' => function (Builder $query) {
                            $query->select(['id', 'name', 'parent_id']);
                        }])->select(['id', 'name', 'parent_id'])->orderBy('name');
                    }]);
            }
            case 2:
                return $this->categoryRepository->all(['id', 'name'], ['name' => 'asc'],
                    ['subCategories' => function (Builder $query) {
                        $query->with(['subCategories' => function (Builder $query) {
                            $query->select(['id', 'name', 'parent_id']);
                        }])->select(['id', 'name', 'parent_id'])->orderBy('name');
                    }]);
            default:
                return $this->categoryRepository->all();
        }

    }
}
