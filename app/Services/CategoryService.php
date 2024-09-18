<?php

namespace App\Services;

use App\Interfaces\CategoryRepositoryInterface;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CategoryService
{
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getCategories(int $mode = null): Collection
    {
        switch ($mode) {
            case 1:
            {
                return $this->categoryRepository->getBy(['parent_id' => null], [], ['id', 'name'], ['name' => 'asc'],
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

    public function getCategory(int $id, int $mode = null): ?Model
    {
        switch ($mode) {
            case 1:
            {
                return $this->categoryRepository->find($id, ['id', 'name', 'parent_id'],
                    ['products' => function (Builder $query) {
                        $query->orderBy('name')->limit(5);
                    }]);
            }
            case 2:
            {
                return $this->categoryRepository->find($id, ['id', 'name', 'parent_id'],
                    ['products' => function (Builder $query) {
                        $query->orderBy('name');
                    }]);
            }
            default:
            {
                return $this->categoryRepository->find($id, ['id', 'name', 'parent_id'],
                    ['subCategories' => function (Builder $query) {
                        $query->with(['subCategories' => function (Builder $query) {
                            $query->select(['id', 'name', 'parent_id']);
                        }])->select(['id', 'name', 'parent_id'])->orderBy('name');
                    }]);
            }
        }

    }

    public function store(array $categoryData): Model
    {
        return $this->categoryRepository->store($categoryData);
    }

    public function update(int $id, array $categoryData): bool|Model
    {
        return $this->categoryRepository->update($id, $categoryData);
    }

    public function destroy(int $id): bool
    {
        return $this->categoryRepository->destroy($id);
    }

    public function updateParentCategory(array $subcategory_ids, int $parent_id = null): bool
    {
        return $this->categoryRepository->updateParentCategory($subcategory_ids, $parent_id);
    }
}
