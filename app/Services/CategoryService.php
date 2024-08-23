<?php

namespace App\Services;

use App\Repositories\CategoryRepository;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function getCategories(CategoryRepository $categoryRepository, int $mode = null): Collection
    {
        switch ($mode) {
            case 1:
            {
                return $categoryRepository->getBy(['parent_id' => null], ['id', 'name'], ['name' => 'asc'],
                    ['subCategories' => function (Builder $query) {
                        $query->with(['subCategories' => function (Builder $query) {
                            $query->select(['id', 'name', 'parent_id']);
                        }])->select(['id', 'name', 'parent_id'])->orderBy('name');
                    }]);
            }
            case 2:
                return $categoryRepository->all(['id', 'name'], ['name' => 'asc'],
                    ['subCategories' => function (Builder $query) {
                        $query->with(['subCategories' => function (Builder $query) {
                            $query->select(['id', 'name', 'parent_id']);
                        }])->select(['id', 'name', 'parent_id'])->orderBy('name');
                    }]);
            default:
                return $categoryRepository->all();
        }

    }
}
