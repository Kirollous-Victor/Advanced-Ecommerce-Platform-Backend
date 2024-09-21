<?php

namespace App\Services;

use App\Interfaces\EmailVerificationInterface;
use App\Interfaces\PasswordResetRepositoryInterface;
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
    private EmailVerificationInterface $emailVerificationRepo;
    private PasswordResetRepositoryInterface $passwordResetRepository;
    private EmailService $emailService;
    private TokenService $tokenService;

    public function __construct(UserRepositoryInterface          $userRepository, EmailService $emailService,
                                EmailVerificationInterface       $emailVerificationRepo, TokenService $tokenService,
                                PasswordResetRepositoryInterface $passwordResetRepository)
    {
        $this->userRepository = $userRepository;
        $this->emailService = $emailService;
        $this->tokenService = $tokenService;
        $this->emailVerificationRepo = $emailVerificationRepo;
        $this->passwordResetRepository = $passwordResetRepository;
    }


    public function login(array $cardinality, bool $remember_me = false): bool|array
    {
        if (Auth::attempt($cardinality)) {
            $this->tokenService->revokeUserTokens();
            return $this->tokenService->generateToken(Auth::user(), $remember_me);
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
                $this->emailVerificationRepo->destroyBy('email', $user->email);
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
        $code = $this->createVerificationCode();
        $this->emailVerificationRepo->store(['email' => $user->email, 'code' => $code]);
        $this->emailService->sendVerificationEmail($user->email, $user->name, $code);
    }

    private function createVerificationCode(): string
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
        $record = $this->emailVerificationRepo->findBy('code', $code);
        if (!$record)
            return false;
        if (Carbon::createFromTimeString($record->created_at)->diff(now())->hours > 2) {
            $this->emailVerificationRepo->destroyBy('code', $code);
            return false;
        }
        $this->userRepository->updateBy('email', $record->email, ['email_verified_at' => now()]);
        $this->emailVerificationRepo->destroyBy('code', $code);
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

    public function sendPasswordResetEmail(string $email): void
    {
        $user = $this->userRepository->isExistsBy('email', $email);
        if ($user) {
            try {
                DB::beginTransaction();
                $token = $this->generateResetPasswordToken($email);
                $url = route('reset.password', compact('token'));
                $this->emailService->sendPasswordResetEmail($email, $url);
                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();
                $this->logException($exception, __CLASS__ . ':' . __METHOD__ . '|' . __LINE__);
                throw $exception;
            }
        }
    }

    private function generateResetPasswordToken(string $email): string
    {
        $token = strtoupper(Str::password(30, symbols: false));
        $this->passwordResetRepository->destroyBy('email', $email);
        $this->passwordResetRepository->store(['email' => $email, 'token' => $token]);
        return $token;
    }

    public function changePassword(string $token, string $password): bool
    {
        $record = $this->passwordResetRepository->findBy('token', $token);
        if (!$record)
            return false;
        if (Carbon::createFromTimeString($record->created_at)->diff(now())->hours > 2) {
            $this->passwordResetRepository->destroyBy('token', $token);
            return false;
        }
        $this->userRepository->updateBy('email', $record->email, ['password' => Hash::make($password)]);
        $this->passwordResetRepository->destroyBy('token', $token);
        return true;
    }
}
