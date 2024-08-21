<?php

namespace App\Repositories;

use App\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function all(): Collection
    {
        return Category::with(['subCategories' => fn(Builder $query) => $query->with('subCategories')
            ->orderBy('name')])->whereNull('parent_id')->orderBy('name')->get(['id', 'name']);
    }

    public function getById($id): ?Category
    {
        return Category::with(['subCategories' => fn(Builder $query) => $query
            ->with(['subCategories' => fn(Builder $query) => $query->orderBy('name')])
            ->orderBy('name')])->find($id);
    }

    public function store(array $data): Category
    {
        return Category::create($data);
    }

    public function update(array $data, $id): bool|Category
    {
        $category = Category::find($id);
        if ($category) {
            $category->update($data);
            return $category;
        }
        return false;
    }

    public function delete($id): bool
    {
        $category = Category::find($id);
        if ($category) {
            return $category->delete();
        }
        return false;
    }
}
