<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CouponService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    protected CouponService $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }


    public function index(): JsonResponse
    {
        $coupons = $this->couponService->all();
        return response()->json(['data' => $coupons]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:10|unique:coupons,code',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => ['required', 'decimal:0,2', 'min:0.01',
                Rule::when($request->discount_type == 'percentage', 'max:100')],
            'expiry_date' => 'nullable|date_format:Y-m-d H:i:s|after:1 hour'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        $coupon = $this->couponService->store($validator->validated());
        return response()->json(['message' => 'Coupon has been created', 'data' => $coupon], 201);
    }

    public function show(int $id): JsonResponse
    {
        $validator = Validator::make(compact('id'), [
            'id' => 'required|integer|exists:coupons,id'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        $category = $this->couponService->find($id);
        return response()->json(['data' => $category]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all() + compact('id'), [
            'id' => 'required|integer|exists:coupons,id',
            'code' => 'sometimes|string|max:10|unique:coupons,code',
            'discount_type' => 'required_with:discount_value|in:percentage,fixed',
            'discount_value' => ['required_with:discount_type', 'decimal:0,2', 'min:0.01',
                Rule::when($request->discount_type == 'percentage', 'max:100')],
            'expiry_date' => 'nullable|date_format:Y-m-d H:i:s'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        $coupon = $this->couponService->update($id, $validator->validated());
        return response()->json(['message' => 'Coupon has been updated', 'data' => $coupon]);
    }

    public function destroy(int $id): JsonResponse
    {
        $validator = Validator::make(compact('id'), [
            'id' => 'required|integer|exists:coupons,id'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        $this->couponService->destroy($id);
        return response()->json(['message' => 'Coupon has been deleted']);
    }
}
