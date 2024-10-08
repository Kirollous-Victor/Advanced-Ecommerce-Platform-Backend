<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller implements HasMiddleware
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show']),
            new Middleware('verified', except: ['index', 'show']),
            new Middleware('permission:manage categories', except: ['index', 'show']),
        ];
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
        $category = $this->categoryService->store($validator->validated());
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
        $category = $this->categoryService->getCategory($id);
        return response()->json(['data' => $category]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all() + compact('id'), [
            'id' => 'required|integer|exists:categories,id',
            'name' => 'sometimes|string|between:1,100',
            'parent_id' => 'nullable|integer|not_in:' . $id . '|exists:categories,id'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        $category = $this->categoryService->update($id, $validator->validated());
        if ($category) {
            return response()->json(['message' => 'Category has been updated', 'data' => $category]);
        }
        return response()->json(['message' => 'Category has not been updated'], 409);

    }

    public function destroy(int $id): JsonResponse
    {
        $validator = Validator::make(compact('id'), [
            'id' => 'required|integer|exists:categories,id'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        if ($this->categoryService->destroy($id)) {
            return response()->json(['message' => 'Category has been deleted']);
        }
        return response()->json(['message' => 'Category has not been deleted'], 409);
    }

    public function moveSubcategories(Request $request, int $parent_id): JsonResponse
    {
        $validator = Validator::make($request->all() + compact('parent_id'), [
            'parent_id' => 'required|integer|exists:categories,id',
            'subcategory_ids' => 'required|array|between:2,50',
            'subcategory_ids.*' => 'integer|distinct|not_in:' . $parent_id . '|exists:categories,id',
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        if ($this->categoryService->updateParentCategory($request->subcategory_ids, $parent_id)) {
            return response()->json(['message' => 'Subcategories moved successfully.']);
        }
        return response()->json(['message' => 'Subcategories not updated.'], 409);
    }

    public function removeSubcategoriesParent(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'subcategory_ids' => 'required|array|between:2,50',
            'subcategory_ids.*' => 'integer|distinct|exists:categories,id',
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        if ($this->categoryService->updateParentCategory($request->subcategory_ids)) {
            return response()->json(['message' => 'Subcategories have been updated.']);
        }
        return response()->json(['message' => 'Subcategories have been not updated.'], 409);
    }
}
