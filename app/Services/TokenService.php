<?php

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;

class TokenService
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function generateToken(User $user, bool $rememberMe = false): array
    {
        $tokenName = $rememberMe ? 'remember_token' : 'auth_token';
        $expiresIn = $rememberMe ? now()->addWeeks(1) : now()->addHour();
        return [
            'token' => $user->createToken($tokenName, ['*'], $expiresIn)->plainTextToken,
            'expires_in' => $expiresIn,
        ];
    }

    public function generateTokenByEmail(string $email): bool|array
    {
        $user = $this->userRepository->findBy('email', $email);
        if ($user)
            /** @var User $user */
            return $this->generateToken($user);
        return false;
    }

    public function revokeUserTokens(User $user = null, bool $current = false): bool
    {
        $user = $user ?? auth()->user();
        return $current ? $user->currentAccessToken()->delete() : $user->tokens()->delete();
    }
}
