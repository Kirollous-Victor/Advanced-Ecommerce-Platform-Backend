<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    protected CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function index(): JsonResponse
    {
        $categories = $this->categoryRepository->all(['id', 'name'], ['name' => 'asc'],
            ['subCategories' => function (Builder $query) {
                $query->with(['subCategories' => function (Builder $query) {
                    $query->select(['id', 'name', 'parent_id']);
                }])->select(['id', 'name', 'parent_id'])->orderBy('name');
            }]);
        return response()->json(['data' => $categories]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'parent_id' => 'nullable|exists:categories,id'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        $category = $this->categoryRepository->store($validator->valid());
        return response()->json(['data' => $category, 'message' => 'Category has been created'], 201);
    }

    public function show(string $id): JsonResponse
    {
        $category = $this->categoryRepository->find($id, ['id', 'name', 'parent_id'], ['subCategories']);
        if ($category) {
            return response()->json(['data' => $category]);
        }
        return response()->json(['message' => 'Category not found'], 404);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|between:1,100',
            'parent_id' => 'nullable|exists:categories,id'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        $category = $this->categoryRepository->update($id, $validator->valid());
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        return response()->json(['message' => 'Category has been updated', 'data' => $category]);
    }

    public function destroy(string $id): JsonResponse
    {
        $category = $this->categoryRepository->destroy($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        return response()->json(['message' => 'Category has been deleted']);
    }
}
