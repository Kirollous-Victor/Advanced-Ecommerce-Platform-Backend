<?php

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Traits\LogTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthService
{
    use LogTrait;

    private UserRepositoryInterface $userRepository;
    private EmailService $emailService;
    private TokenService $tokenService;

    public function __construct(UserRepositoryInterface $userRepository, EmailService $emailService, TokenService $tokenService)
    {
        $this->userRepository = $userRepository;
        $this->emailService = $emailService;
        $this->tokenService = $tokenService;
    }


    public function login(array $cardinality, bool $remember_me = false): bool|array
    {
        if (Auth::attempt($cardinality)) {
            $this->tokenService->revokeUserTokens();
            $token = $this->tokenService->generateToken(Auth::user());
            return $token;
        }
        return false;
    }

    public function register(array $userData): void
    {
        DB::beginTransaction();
        try {
            $userData['password'] = Hash::make($userData['password']);
            $userData['role'] = 'user';
            $user = $this->userRepository->store($userData);
            $this->sendVerificationCode($user);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->logException($exception, __CLASS__ . ':' . __METHOD__ . '|' . __LINE__);
            throw $exception;
        }
    }

    public function resendVerificationCode(string $email): bool
    {
        $user = $this->userRepository->findBy('email', $email);
        if (!$user->email_verified_at) {
            DB::beginTransaction();
            try {
                DB::table('email_verification')->where('email', $user->email)->delete();
                $this->sendVerificationCode($user);
                DB::commit();
                return true;
            } catch (\Exception $exception) {
                DB::rollBack();
                $this->logException($exception, __CLASS__ . ':' . __METHOD__ . '|' . __LINE__);
                throw $exception;
            }
        } else {
            return false;
        }
    }

    private function sendVerificationCode(User $user): void
    {
        $code = $this->generateUniqueVerificationCode();
        DB::table('email_verification')->insert(['email' => $user->email, 'code' => $code]);
        $this->emailService->sendVerificationEmail($user->email, $user->name, $code);
    }

    private function generateUniqueVerificationCode(): string
    {
        do {
            $code = strtoupper(Str::password(7, symbols: false));
            $validator = Validator::make(compact('code'), [
                'code' => 'unique:email_verification,code',
            ]);
        } while ($validator->fails());
        return $code;
    }

    public function verifyEmail(string $code): bool|array
    {
        $record = DB::table('email_verification')->where(compact('code'))->first();
        if (!$record)
            return false;
        if (Carbon::createFromTimeString($record->created_at)->diff(now())->hours > 2) {
            DB::table('email_verification')->where(compact('code'))->delete();
            return false;
        }
        $this->userRepository->updateBy('email', $record->email, ['email_verified_at' => now()]);
        DB::table('email_verification')->where(compact('code'))->delete();
        return $this->tokenService->generateTokenByEmail($record->email);
    }

    public function logout(): bool
    {
        return $this->tokenService->revokeUserTokens();
    }

    public function refreshToken(): bool|array
    {
        $user = auth()->user();
        if ($this->tokenService->revokeUserTokens(current: true)) {
            $token['expires_in'] = now()->addHour();
            $token['token'] = $user->createToken('auth_token', ['*'], $token['expires_in'])->plainTextToken;
            return $token;
        }
        return false;
    }
}
