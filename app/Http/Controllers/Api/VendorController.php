<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\VendorRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VendorController extends Controller
{
    protected VendorRepositoryInterface $vendorRepository;

    public function __construct(VendorRepositoryInterface $vendorRepository)
    {
        $this->vendorRepository = $vendorRepository;
    }

    public function index(): JsonResponse
    {
        $vendors = $this->vendorRepository->all(['id', 'user_id', 'store_name', 'description']);
        return response()->json(['data' => $vendors]);
    }

    public function store(Request $request)
    {
        //
    }


    public function show(int $id): JsonResponse
    {
        $validator = Validator::make(compact('id'), [
            'id' => 'required|integer|exists:vendors,id'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        $vendor = $this->vendorRepository->find($id, ['id', 'user_id', 'store_name', 'description'],
            ['user' => function ($query) {
                $query->select(['id', 'name', 'email']);
            }]);
        return response()->json(['data' => $vendor]);
    }

    public function update(Request $request, int $id)
    {
        //
    }

    public function destroy(int $id)
    {
        //
    }
}
