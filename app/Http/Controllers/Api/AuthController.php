<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'bail|required|string|max:100',
            'email' => 'bail|required|string|max:50|email:rfc,dns|unique:users,email',
            'password' => 'bail|required|string|between:8,16|' .
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$_\-+*!?:]).+$/'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        try {
            $this->authService->register($validator->validated());
            return response()->json(['message' => 'Account created Successfully'], 201);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Something went wrong, try again later'], 500);
        }
    }

    public function verifyEmail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'bail|required|string|size:7',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        $token = $this->authService->verifyEmail($validator->validated()['code']);
        if ($token) {
            return response()->json(['message' => 'Email verified Successfully', 'access_token' => $token,
                'token_type' => 'Bearer']);
        }
        return response()->json(['message' => 'Code not found or may expired'], 422);
    }

    public function resendVerificationCode(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'bail|required|string|max:50|email:rfc,dns|exists:users,email',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        try {
            if ($this->authService->resendVerificationCode($validator->validated()['email']))
                return response()->json(['message' => 'New code has been generated and sent to email']);
            return response()->json(['message' => 'No verification needed']);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Something went wrong, try again later'], 500);
        }
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|max:50|email:rfc,dns',
            'password' => 'required|string|between:8,16'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        $token = $this->authService->login($validator->validated());
        if ($token) {
            return response()->json(['message' => 'Login success', 'access_token' => $token,
                'token_type' => 'Bearer']);
        }
        return response()->json(['error' => 'Your Email or Password may be wrong, please try again.'], 401);
    }

    public function logout(Request $request): JsonResponse
    {
        auth()->user()->tokens()->delete();
        return response()->json(['message' => 'Logout successfully']);
    }

    public function userProfile(Request $request)
    {

    }
}
