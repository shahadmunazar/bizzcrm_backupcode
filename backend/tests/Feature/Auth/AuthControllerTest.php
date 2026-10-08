<?php

namespace Tests\Feature\Auth;

use App\Jobs\DepartmentCreateJob;
use App\Jobs\SendOtpJob;
use App\Jobs\SendRegistrationVerifyJob;
use App\Jobs\SendResetPasswordEmailJob;
use App\Models\Box;
use App\Models\BoxUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
    }

    /** @test */
    public function it_displays_login_form()
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    /** @test */
    public function it_displays_forget_password_form()
    {
        $response = $this->get('/forget-password');

        $response->assertStatus(200);
        $response->assertViewIs('auth.forget');
    }

    /** @test */
    public function it_displays_register_form()
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }

    /** @test */
    public function it_displays_reset_password_page()
    {
        $response = $this->get('/reset-password');

        $response->assertStatus(200);
        $response->assertViewIs('auth.reset_passowrd');
    }

    /** @test */
    public function it_displays_mobile_login_page()
    {
        $response = $this->get('/login-mobile');

        $response->assertStatus(200);
        $response->assertViewIs('auth.mobile');
    }

    /** @test */
    public function it_sends_otp_successfully_via_email()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/send-otp', [
            'user_input' => 'test@example.com',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'OTP sent successfully and valid for 10 minutes.',
        ]);

        $user->refresh();
        $this->assertNotNull($user->otp);
        $this->assertNotNull($user->otp_expires_at);

        Queue::assertPushed(SendOtpJob::class);
    }

    /** @test */
    public function it_fails_to_send_otp_when_user_not_found()
    {
        $response = $this->postJson('/send-otp', [
            'user_input' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'User not found for the given mobile or email.',
        ]);
    }

    /** @test */
    public function it_returns_error_when_email_not_verified()
    {
        $user = User::factory()->create([
            'email' => 'unverified@example.com',
            'email_verified_at' => null,
        ]);

        $response = $this->postJson('/send-otp', [
            'user_input' => 'unverified@example.com',
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'status' => 403,
            'success' => false,
        ]);
    }

    /** @test */
    public function it_requires_user_input_to_send_otp()
    {
        $response = $this->postJson('/send-otp', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['user_input']);
    }

    /** @test */
    public function it_changes_password_successfully()
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword'),
        ]);

        $this->actingAs($user);

        $response = $this->postJson('/change-password', [
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 200,
            'message' => 'Password changed successfully.',
        ]);

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    /** @test */
    public function it_requires_password_confirmation()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/change-password', [
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'differentpassword',
        ]);

        $response->assertStatus(403);
        $response->assertJsonValidationErrors(['new_password']);
    }

    /** @test */
    public function it_requires_minimum_password_length()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/change-password', [
            'new_password' => 'short',
            'new_password_confirmation' => 'short',
        ]);

        $response->assertStatus(403);
        $response->assertJsonValidationErrors(['new_password']);
    }

    /** @test */
    public function it_verifies_otp_successfully()
    {
        $otp = '123456';
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'otp' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        $box = Box::factory()->create();
        BoxUser::factory()->create([
            'user_id' => $user->id,
            'box_id' => $box->id,
        ]);

        $response = $this->postJson('/verify-otp', [
            'user_input' => 'test@example.com',
            'otp' => $otp,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'OTP verified successfully.',
        ]);
        $response->assertJsonStructure(['redirect_to']);
    }

    /** @test */
    public function it_fails_verification_with_invalid_otp()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'otp' => '123456',
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        $response = $this->postJson('/verify-otp', [
            'user_input' => 'test@example.com',
            'otp' => '654321',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'type' => 'invalid_otp',
            'message' => 'The OTP you entered is incorrect.',
        ]);
    }

    /** @test */
    public function it_fails_verification_with_expired_otp()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'otp' => '123456',
            'otp_expires_at' => Carbon::now()->subMinutes(5),
        ]);

        $response = $this->postJson('/verify-otp', [
            'user_input' => 'test@example.com',
            'otp' => '123456',
        ]);

        $response->assertStatus(410);
        $response->assertJson([
            'success' => false,
            'type' => 'expired_otp',
            'message' => 'Your OTP has expired. Please resend OTP.',
        ]);
    }

    /** @test */
    public function it_resets_password_successfully()
    {
        $token = 'valid-reset-token';
        $user = User::factory()->create([
            'remember_token' => $token,
        ]);

        $response = $this->postJson('/reset-password', [
            'token' => $token,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Password reset successful!',
        ]);

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
        $this->assertNull($user->remember_token);
    }

    /** @test */
    public function it_fails_password_reset_with_invalid_token()
    {
        $response = $this->postJson('/reset-password', [
            'token' => 'invalid-token',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Invalid or expired token.',
        ]);
    }

    /** @test */
    public function it_sends_forget_password_email()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/forget-email', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'If your email exists in our system, you will receive a password reset link shortly.',
        ]);

        $user->refresh();
        $this->assertNotNull($user->remember_token);

        Queue::assertPushed(SendResetPasswordEmailJob::class);
    }

    /** @test */
    public function it_returns_success_even_for_nonexistent_email()
    {
        $response = $this->postJson('/forget-email', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'If your email exists in our system, you will receive a password reset link shortly.',
        ]);
    }

    /** @test */
    public function it_fails_forget_password_for_unverified_email()
    {
        $user = User::factory()->create([
            'email' => 'unverified@example.com',
            'email_verified_at' => null,
        ]);

        $response = $this->postJson('/forget-email', [
            'email' => 'unverified@example.com',
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'status' => 403,
        ]);
    }

    /** @test */
    public function it_verifies_user_email_successfully()
    {
        $token = 'verification-token';
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'remember_token' => $token,
            'email_verified_at' => null,
        ]);

        $box = Box::factory()->create();
        $boxUser = BoxUser::factory()->create([
            'user_id' => $user->id,
            'box_id' => $box->id,
            'status' => 'Disabled',
        ]);

        $response = $this->get("/verify/{$token}");

        $response->assertRedirect('/login');
        $response->assertSessionHas('success', 'Your account has been verified. You may now login.');

        $user->refresh();
        $boxUser->refresh();

        $this->assertNotNull($user->email_verified_at);
        $this->assertEquals('Enabled', $boxUser->status);
    }

    /** @test */
    public function it_fails_verification_with_invalid_token()
    {
        $response = $this->get('/verify/invalid-token');

        $response->assertRedirect('/login');
        $response->assertSessionHas('error', 'Invalid or expired verification link.');
    }

    /** @test */
    public function it_searches_countries_successfully()
    {
        $response = $this->getJson('/search-country?type=country&keyword=ind');

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    /** @test */
    public function it_searches_states_by_country()
    {
        $response = $this->getJson('/search-country?type=state&country_id=101');

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    /** @test */
    public function it_requires_country_id_for_state_search()
    {
        $response = $this->getJson('/search-country?type=state');

        $response->assertStatus(400);
        $response->assertJson(['error' => 'country_id required']);
    }

    /** @test */
    public function it_searches_cities_by_state()
    {
        $response = $this->getJson('/search-country?type=city&state_id=1');

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    /** @test */
    public function it_requires_state_id_for_city_search()
    {
        $response = $this->getJson('/search-country?type=city');

        $response->assertStatus(400);
        $response->assertJson(['error' => 'state_id required']);
    }

    /** @test */
    public function it_registers_user_successfully()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'company_name' => 'Test Company',
            'industry' => 'Technology',
            'country_id' => '101',
            'state_id' => '1',
            'city_id' => '1',
            'pincode' => '123456',
            'address' => '123 Test Street',
        ];

        $response = $this->postJson('/register', $data);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Registration completed successfully. Please check your email to verify your account.',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'name' => 'John Doe',
        ]);

        $this->assertDatabaseHas('boxes', [
            'name' => 'Test Company',
            'slug' => 'test-company',
        ]);

        Queue::assertPushed(SendRegistrationVerifyJob::class);
        Queue::assertPushed(DepartmentCreateJob::class);
    }

    /** @test */
    public function it_requires_all_registration_fields()
    {
        $response = $this->postJson('/register', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'name', 'email', 'phone', 'company_name',
            'industry', 'country_id', 'state_id', 'city_id',
            'pincode', 'address'
        ]);
    }

    /** @test */
    public function it_prevents_duplicate_email_registration()
    {
        $user = User::factory()->create([
            'email' => 'existing@example.com',
            'email_verified_at' => now(),
        ]);

        $data = [
            'name' => 'John Doe',
            'email' => 'existing@example.com',
            'phone' => '1234567890',
            'company_name' => 'Test Company',
            'industry' => 'Technology',
            'country_id' => '101',
            'state_id' => '1',
            'city_id' => '1',
            'pincode' => '123456',
            'address' => '123 Test Street',
        ];

        $response = $this->postJson('/register', $data);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'errors' => 'Email is already taken and verified.',
        ]);
    }

    /** @test */
    public function it_prevents_duplicate_company_name()
    {
        $box = Box::factory()->create([
            'slug' => 'test-company',
            'name' => 'Test Company',
        ]);

        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'company_name' => 'Test Company',
            'industry' => 'Technology',
            'country_id' => '101',
            'state_id' => '1',
            'city_id' => '1',
            'pincode' => '123456',
            'address' => '123 Test Street',
        ];

        $response = $this->postJson('/register', $data);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'errors' => 'This Company Name is Already taken.',
        ]);
    }

    /** @test */
    public function it_resends_verification_email_successfully()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'email_verified_at' => null,
        ]);

        $response = $this->postJson('/resend-verification', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Check your email inbox to verify your email, then come back here to Login.',
        ]);

        Queue::assertPushed(SendRegistrationVerifyJob::class);
    }

    /** @test */
    public function it_fails_to_resend_for_already_verified_email()
    {
        $user = User::factory()->create([
            'email' => 'verified@example.com',
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/resend-verification', [
            'email' => 'verified@example.com',
        ]);

        $response->assertStatus(409);
        $response->assertJson([
            'success' => false,
            'message' => 'Email is already verified.',
        ]);
    }

    /** @test */
    public function it_logs_in_user_successfully()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $box = Box::factory()->create();
        BoxUser::factory()->create([
            'user_id' => $user->id,
            'box_id' => $box->id,
            'status' => 'Enabled',
        ]);

        $response = $this->postJson('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['redirect']);
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function it_fails_login_with_incorrect_password()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'error' => 'Incorrect password. Please try again.',
        ]);
        $this->assertGuest();
    }

    /** @test */
    public function it_fails_login_for_nonexistent_user()
    {
        $response = $this->postJson('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'error' => 'We couldn\'t find an account with this email address.',
        ]);
    }

    /** @test */
    public function it_fails_login_for_unverified_email()
    {
        $user = User::factory()->create([
            'email' => 'unverified@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => null,
        ]);

        $response = $this->postJson('/login', [
            'email' => 'unverified@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(409);
        $response->assertJson([
            'success' => false,
            'resend' => true,
        ]);
    }

    /** @test */
    public function it_fails_login_for_disabled_user()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $box = Box::factory()->create();
        BoxUser::factory()->create([
            'user_id' => $user->id,
            'box_id' => $box->id,
            'status' => 'Disabled',
        ]);

        $response = $this->postJson('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'error' => 'Your account is currently disabled. Please contact admin.',
        ]);
        $this->assertGuest();
    }

    /** @test */
    public function it_logs_out_user_successfully()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/logout', [
            'password' => 'password',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    /** @test */
    public function it_validates_email_format()
    {
        $response = $this->postJson('/login', [
            'email' => 'invalid-email',
            'password' => 'password123',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function it_validates_phone_number_length()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '123', // Too short
            'company_name' => 'Test Company',
            'industry' => 'Technology',
            'country_id' => '101',
            'state_id' => '1',
            'city_id' => '1',
            'pincode' => '123456',
            'address' => '123 Test Street',
        ];

        $response = $this->postJson('/register', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['phone']);
    }

    /** @test */
    public function it_validates_pincode_format()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'company_name' => 'Test Company',
            'industry' => 'Technology',
            'country_id' => '101',
            'state_id' => '1',
            'city_id' => '1',
            'pincode' => '12', // Too short
            'address' => '123 Test Street',
        ];

        $response = $this->postJson('/register', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['pincode']);
    }
}

