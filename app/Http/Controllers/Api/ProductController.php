<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\ProductRepositoryInterface;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    protected ProductRepositoryInterface $productRepository;
    protected ProductService $productService;

    public function __construct(ProductRepositoryInterface $productRepository, ProductService $productService)
    {
        $this->productRepository = $productRepository;
        $this->productService = $productService;
    }


    public function index(): JsonResponse
    {
        $products = $this->productRepository->all();
        return response()->json(['data' => $products]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required|integer|exists:vendors,id',
            'category_id' => 'required|integer|exists:categories,id',
            'name' => 'required|string|between:1,100',
            'description' => 'required|string|max:255',
            'price' => 'required|decimal:0,2|min:0.01',
            'stock' => 'required|integer|min:0',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        $category = $this->productRepository->store($validator->valid());
        return response()->json(['data' => $category, 'message' => 'Product has been created'], 201);
    }

    public function show(int $id): JsonResponse
    {
        $validator = Validator::make(compact('id'), [
            'id' => 'required|integer|exists:products,id'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        $product = $this->productService->find($id, 1);
        return response()->json(['data' => $product]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all() + compact('id'), [
            'id' => 'required|integer|exists:products,id',
            'vendor_id' => 'sometimes|integer|exists:vendors,id',
            'category_id' => 'nullable|integer|exists:categories,id',
            'name' => 'sometimes|string|between:1,100',
            'description' => 'sometimes|string|max:255',
            'price' => 'sometimes|decimal:0,2|min:0.01',
            'stock' => 'sometimes|integer|min:0',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        $product = $this->productRepository->update($id, $validator->validated());
        return response()->json(['message' => 'Product has been updated', 'data' => $product]);
    }

    public function destroy(int $id): JsonResponse
    {
        $validator = Validator::make(compact('id'), [
            'id' => 'required|integer|exists:products,id'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        $this->productRepository->destroy($id);
        return response()->json(['message' => 'Product has been deleted']);
    }
}
