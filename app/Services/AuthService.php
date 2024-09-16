<?php

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use App\Mail\VerificationEmail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthService
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(array $cardinality): bool|string
    {
        if (Auth::attempt($cardinality)) {
            return Auth::user()->createToken('auth_token')->plainTextToken;
        }
        return false;
    }

    public function loginByEmail(string $email): bool|string
    {
        $user = $this->userRepository->findBy('email', $email);
        if ($user)
            return $user->createToken('auth_token')->plainTextToken;
        return false;
    }

    public function register(array $userData): void
    {
        DB::beginTransaction();
        try {
            $userData['password'] = Hash::make($userData['password']);
            $userData['role'] = 'user';
            $user = $this->userRepository->store($userData);
            $code = $this->generateUniqueVerificationCode();
            $emailData = ['email' => $user->email, 'code' => $code];
            DB::table('email_verification')->insert($emailData);
            $emailData['name'] = $user['name'];
            Mail::to($user->email)->send(new VerificationEmail($emailData));
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function generateUniqueVerificationCode(): string
    {
        do {
            $code = strtoupper(Str::password(7, symbols: false));
            $validator = Validator::make(compact('code'), [
                'code' => 'unique:email_verification,code',
            ]);
        } while ($validator->fails());
        return $code;
    }

    public function verifyEmail(string $code): bool|string
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
        return $this->loginByEmail($record->email);
    }


}
