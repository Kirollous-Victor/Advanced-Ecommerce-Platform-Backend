<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\CategoryRepositoryInterface;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    protected CategoryRepositoryInterface $categoryRepository;
    protected CategoryService $categoryService;

    public function __construct(CategoryRepositoryInterface $categoryRepository, CategoryService $categoryService)
    {
        $this->categoryRepository = $categoryRepository;
        $this->categoryService = $categoryService;
    }

    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getCategories(1);
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

    public function show(int $id): JsonResponse
    {
        $validator = Validator::make(compact('id'), [
            'id' => 'required|integer|exists:categories,id'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        $category = $this->categoryRepository->find($id, ['id', 'name', 'parent_id'], ['subCategories']);
        return response()->json(['data' => $category]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all() + compact('id'), [
            'name' => 'sometimes|string|between:1,100',
            'parent_id' => 'nullable|exists:categories,id',
            'id' => 'required|integer|exists:categories,id'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        $category = $this->categoryRepository->update($id, $validator->valid());
        return response()->json(['message' => 'Category has been updated', 'data' => $category]);
    }

    public function destroy(int $id): JsonResponse
    {
        $validator = Validator::make(compact('id'), [
            'id' => 'required|integer|exists:categories,id'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        $deleted = $this->categoryRepository->destroy($id);
        return response()->json(['message' => 'Category has been deleted']);
    }

    public function moveSubcategories(Request $request, int $parent_id): JsonResponse
    {
        $validator = Validator::make($request->all() + compact('parent_id'), [
            'parent_id' => 'required|integer|exists:categories,id',
            'subcategory_ids' => 'required|array',
            'subcategory_ids.*' => 'integer|distinct|not_in:' . $parent_id . '|exists:categories,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        try {
            $this->categoryRepository->updateParentCategory($request->subcategory_ids, $parent_id);
            return response()->json(['message' => 'Subcategories moved successfully.']);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }
}
