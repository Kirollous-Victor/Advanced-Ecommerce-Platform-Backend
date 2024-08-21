<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => Category::all()]);
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
        Category::create($validator->valid());
        return response()->json(['message' => 'Category has been created'], 201);
    }

    public function show(string $id): JsonResponse
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        return response()->json(['data' => $category]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'parent_id' => 'nullable|exists:categories,id'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        $category->update($validator->valid());
        return response()->json(['message' => 'Category has been updated', 'data' => $category]);
    }

    public function destroy(string $id): JsonResponse
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        $category->delete();
        return response()->json(['message' => 'Category has been deleted']);
    }
}
