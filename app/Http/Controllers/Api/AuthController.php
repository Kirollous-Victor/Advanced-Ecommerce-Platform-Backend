<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected AuthService $authService;
    protected UserService $userService;

    public function __construct(AuthService $authService, UserService $userService)
    {
        $this->authService = $authService;
        $this->userService = $userService;
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
            return response()->json(['error' => 'Something went wrong, try again later'], 500);
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
            return response()->json(['message' => 'Email verified Successfully', 'access_token' => $token['token'],
                'token_type' => 'Bearer', 'expires_in' => $token['expires_in']]);
        }
        return response()->json(['error' => 'Code not found or may expired'], 422);
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
            return response()->json(['error' => 'Something went wrong, try again later'], 500);
        }
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|max:50|email:rfc,dns',
            'password' => 'required|string|between:8,16',
            'remember_me' => 'required|boolean'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        $cardinality = $request->only(['email', 'password']);
        $token = $this->authService->login($cardinality, $request->remember_me);
        if ($token) {
            return response()->json(['message' => 'Login success', 'access_token' => $token['token'],
                'token_type' => 'Bearer', 'expires_in' => $token['expires_in']]);
        }
        return response()->json(['error' => 'Your Email or Password may be wrong, please try again.'], 401);
    }

    public function refreshToken(): JsonResponse
    {
        $token = $this->authService->refreshToken();
        if ($token)
            return response()->json(['access_token' => $token['token'], 'token_type' => 'Bearer',
                'expires_in' => $token['expires_in']]);
        return response()->json(['error' => 'Something went wrong, try again later'], 500);
    }

    public function forgetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|max:50|email:rfc,dns',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        try {
            $this->authService->sendPasswordResetEmail($request->email);
            return response()->json(['message' => 'Password reset email sent successfully.']);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Failed to send reset email, please try again later.'], 500);
        }
    }

    public function resetPassword(string $token, Request $request): JsonResponse
    {
        $validator = Validator::make($request->all() + compact('token'), [
            'token' => 'required|string|size:30|exists:password_reset_tokens,token',
            'password' => 'bail|required|string|between:8,16|' .
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$_\-+*!?:]).+$/',
            'confirm_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }
        if ($this->authService->changePassword($token, $request->password))
            return response()->json(['message' => 'Password has been changed successfully.']);
        return response()->json(['message' => 'Failed to send change password, please try again later.'], 500);

    }

    public function logout(): JsonResponse
    {
        if ($this->authService->logout())
            return response()->json(['message' => 'Logout successfully']);
        return response()->json(['error' => 'Something went wrong, try again later'], 500);
    }

    public function userProfile(): JsonResponse
    {
        $userData = $this->userService->getUserData();
        return response()->json(['data' => $userData]);
    }
}
