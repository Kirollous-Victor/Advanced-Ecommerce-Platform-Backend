<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test user registration, login, email verification, and fetching profile.
     */
    public function testUserRegistrationAndLoginFlow()
    {
        // Registration
        $response = $this->postJson(route('register'), [
            'name' => 'John Doe',
            'email' => 'johndoe@yahoo.com',
            'password' => 'Password123!'
        ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Account created Successfully']);

        // Get the registered user from database
        $user = User::where('email', 'johndoe@yahoo.com')->first();
        $this->assertNotNull($user);

        // Retrieve verification code from database
//        $verificationCode = VerificationCode::where('user_id', $user->id)->first();
        $verificationCode = DB::table('email_verification')->where('email', $user->email)->first();
        $this->assertNotNull($verificationCode);

        // Verify email
        $response = $this->postJson(route('verify.email'), [
            'code' => $verificationCode->code
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Email verified Successfully']);

        // Login
        $response = $this->postJson(route('login'), [
            'email' => 'johndoe@yahoo.com',
            'password' => 'Password123!',
            'remember_me' => true
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'access_token', 'token_type', 'expires_in']);

        // Get user profile
        $this->withHeaders(['Authorization' => 'Bearer ' . $response['access_token']])
            ->getJson(route('profile'))
            ->assertStatus(200)
            ->assertJson(['data' => $user->id]);
    }

    /**
     * Test forget password and reset password flow.
     */
    public function testForgetAndResetPassword()
    {
        // Create a user manually
        User::factory()->create([
            'email' => 'resetuser@yahoo.com',
            'password' => Hash::make('OldPassword123!')
        ]);

        // Forget password
        $response = $this->postJson(route('forget.password'), [
            'email' => 'resetuser@yahoo.com',
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Password reset email sent successfully.']);

        // Retrieve the password reset token from the database (assuming you have a PasswordReset model)
        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', 'resetuser@yahoo.com')->first();
        $this->assertNotNull($passwordReset);

        // Reset password
        $response = $this->patchJson(route('reset.password', ['token' => $passwordReset->token]), [
            'password' => 'NewPassword123!',
            'confirm_password' => 'NewPassword123!'
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Password has been changed successfully.']);

        // Login with new password
        $response = $this->postJson(route('login'), [
            'email' => 'resetuser@yahoo.com',
            'password' => 'NewPassword123!',
            'remember_me' => false
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'access_token', 'token_type', 'expires_in']);
    }

    /**
     * Test resend verification code.
     */
    public function testResendVerificationCode()
    {
        // Register a user
        $this->postJson(route('register'), [
            'name' => 'Jane Doe',
            'email' => 'janedoe@yahoo.com',
            'password' => 'Password123!'
        ]);

        $user = User::where('email', 'janedoe@yahoo.com')->first();
        $this->assertNotNull($user);

        $oldVerificationCode = DB::table('email_verification')->where('email', $user->email)
            ->value('code');
        // Resend verification code
        $response = $this->postJson(route('resend.verification.code'), [
            'email' => 'janedoe@yahoo.com'
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'New code has been generated and sent to email']);

        // Get the new verification code from the database
        $newVerificationCode = DB::table('email_verification')->where('email', $user->email)
            ->value('code');

        // Ensure the new code is different from the old code
        $this->assertNotEquals($oldVerificationCode, $newVerificationCode,
            'New verification code should not be equal to the old one.');
    }

    /**
     * Test refreshing token.
     */
    public function testRefreshToken()
    {
        // Create a user and log in to get a token
        $user = User::factory()->create([
            'password' => Hash::make('Password123!')
        ]);

        $response = $this->postJson(route('login'), [
            'email' => $user->email,
            'password' => 'Password123!',
            'remember_me' => true
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['access_token', 'token_type', 'expires_in']);

        $firstToken = $response['access_token'];

        // Refresh token
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $response['access_token']])
            ->postJson(route('refresh.token'))
            ->assertStatus(200)
            ->assertJsonStructure(['access_token', 'token_type', 'expires_in']);

        $secondToken = $response['access_token'];
        $this->assertNotEquals($firstToken, $secondToken,
            'New Token should not be equal to the old one.');

        $this->withHeaders(['Authorization' => 'Bearer ' . $response['access_token']])
            ->getJson(route('profile'))
            ->assertStatus(200)
            ->assertJson(['data' => $user->id]);
    }

    /**
     * Test logout.
     */
    public function testLogout()
    {
        $this->withHeaders(['Authorization' => 'Bearer '])
            ->getJson(route('categories.index'))
            ->assertStatus(401);

        // Create a user and log in to get a token
        $user = User::factory()->create([
            'password' => Hash::make('Password123!')
        ]);

        $response = $this->postJson(route('login'), [
            'email' => $user->email,
            'password' => 'Password123!',
            'remember_me' => true
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['access_token', 'token_type', 'expires_in']);

        $this->withHeaders(['Authorization' => 'Bearer ' . $response['access_token']])
            ->getJson(route('categories.index'))
            ->assertStatus(200)
            ->assertJsonStructure(['data']);

        // Logout
        $this->withHeaders(['Authorization' => 'Bearer ' . $response['access_token']])
            ->postJson(route('logout'))
            ->assertStatus(200)
            ->assertJson(['message' => 'Logout successfully']);

        /**
         * It returns 200 unless you use refreshApplication() and remove DatabaseTransactions Trait
         * you can try same scenario on postman it will work correctly
         */
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $response['access_token']])
            ->getJson(route('categories.index'))
            ->assertStatus(401);
    }
}
