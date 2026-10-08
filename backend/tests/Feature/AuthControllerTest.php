<?php

namespace Tests\Feature;

use App\Jobs\SendOtpJob;
use App\Jobs\SendRegistrationVerifyJob;
use App\Jobs\SendResetPasswordEmailJob;
use App\Models\Box;
use App\Models\BoxUser;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Create necessary roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'employee']);
        Role::create(['name' => 'manager']);
        Role::create(['name' => 'superadmin']);
        Role::create(['name' => 'leads']);
        Role::create(['name' => 'deals']);
    }

    // ==================== SEND OTP TESTS ====================

    /** @test */
    public function it_can_send_otp_to_verified_user()
    {
        Queue::fake();

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'mobile' => '1234567890',
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/send-otp', [
            'user_input' => $user->email,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'OTP sent successfully and valid for 10 minutes.',
            ]);

        Queue::assertPushed(SendOtpJob::class);

        $user->refresh();
        $this->assertNotNull($user->otp);
        $this->assertNotNull($user->otp_expires_at);
    }

    /** @test */
    public function it_cannot_send_otp_to_unverified_email()
    {
        $user = User::factory()->create([
            'email' => 'unverified@example.com',
            'email_verified_at' => null,
        ]);

        $response = $this->postJson('/send-otp', [
            'user_input' => $user->email,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 403,
                'success' => false,
            ]);
    }

    /** @test */
    public function it_returns_404_for_non_existent_user()
    {
        $response = $this->postJson('/send-otp', [
            'user_input' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'User not found for the given mobile or email.',
            ]);
    }

    /** @test */
    public function it_requires_user_input_for_send_otp()
    {
        $response = $this->postJson('/send-otp', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['user_input']);
    }

    // ==================== VERIFY OTP TESTS ====================

    /** @test */
    public function it_can_verify_valid_otp()
    {
        $box = Box::factory()->create();
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'otp' => '123456',
            'otp_expires_at' => Carbon::now()->addMinutes(10),
            'email_verified_at' => now(),
        ]);

        BoxUser::factory()->create([
            'user_id' => $user->id,
            'box_id' => $box->id,
            'status' => 'Enabled',
        ]);

        $user->assignRole('employee');

        $response = $this->postJson('/verify-otp', [
            'user_input' => $user->email,
            'otp' => '123456',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'OTP verified successfully.',
            ]);
    }

    /** @test */
    public function it_rejects_invalid_otp()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'otp' => '123456',
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        $response = $this->postJson('/verify-otp', [
            'user_input' => $user->email,
            'otp' => '999999',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'type' => 'invalid_otp',
                'message' => 'The OTP you entered is incorrect.',
            ]);
    }

    /** @test */
    public function it_rejects_expired_otp()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'otp' => '123456',
            'otp_expires_at' => Carbon::now()->subMinutes(1),
        ]);

        $response = $this->postJson('/verify-otp', [
            'user_input' => $user->email,
            'otp' => '123456',
        ]);

        $response->assertStatus(410)
            ->assertJson([
                'success' => false,
                'type' => 'expired_otp',
            ]);
    }

    // ==================== CHANGE PASSWORD TESTS ====================

    /** @test */
    public function authenticated_user_can_change_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword'),
        ]);

        $this->actingAs($user);

        $response = $this->postJson('/change-password', [
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
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

        $response->assertStatus(403)
            ->assertJsonValidationErrors(['new_password']);
    }

    /** @test */
    public function it_requires_minimum_8_characters_for_password()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/change-password', [
            'new_password' => 'short',
            'new_password_confirmation' => 'short',
        ]);

        $response->assertStatus(403)
            ->assertJsonValidationErrors(['new_password']);
    }

    // ==================== FORGET EMAIL TESTS ====================

    /** @test */
    public function it_can_send_password_reset_email()
    {
        Queue::fake();

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/forget-password', [
            'email' => $user->email,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'If your email exists in our system, you will receive a password reset link shortly.',
            ]);

        Queue::assertPushed(SendResetPasswordEmailJob::class);

        $user->refresh();
        $this->assertNotNull($user->remember_token);
    }

    /** @test */
    public function it_does_not_reveal_non_existent_email()
    {
        $response = $this->postJson('/forget-password', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'If your email exists in our system, you will receive a password reset link shortly.',
            ]);
    }

    /** @test */
    public function it_rejects_unverified_email_for_password_reset()
    {
        $user = User::factory()->create([
            'email' => 'unverified@example.com',
            'email_verified_at' => null,
        ]);

        $response = $this->postJson('/forget-password', [
            'email' => $user->email,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'status' => 403,
            ]);
    }

    // ==================== RESET PASSWORD TESTS ====================

    /** @test */
    public function it_can_reset_password_with_valid_token()
    {
        $user = User::factory()->create([
            'remember_token' => 'valid-token-123',
            'password' => Hash::make('oldpassword'),
        ]);

        $response = $this->postJson('/reset-password', [
            'token' => 'valid-token-123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Password reset successful!',
            ]);

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
        $this->assertNull($user->remember_token);
    }

    /** @test */
    public function it_rejects_invalid_reset_token()
    {
        $response = $this->postJson('/reset-password', [
            'token' => 'invalid-token',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid or expired token.',
            ]);
    }

    // ==================== EMAIL VERIFICATION TESTS ====================

    /** @test */
    public function it_can_verify_email_with_valid_token()
    {
        $box = Box::factory()->create();
        $user = User::factory()->create([
            'remember_token' => 'verify-token-123',
            'email_verified_at' => null,
        ]);

        $boxUser = BoxUser::factory()->create([
            'user_id' => $user->id,
            'box_id' => $box->id,
            'status' => 'Disabled',
        ]);

        $response = $this->get('/verify/verify-token-123');

        $response->assertRedirect('/login');
        $response->assertSessionHas('success', 'Your account has been verified. You may now login.');

        $user->refresh();
        $boxUser->refresh();

        $this->assertNotNull($user->email_verified_at);
        $this->assertEquals('Enabled', $boxUser->status);
    }

    /** @test */
    public function it_handles_invalid_verification_token()
    {
        $response = $this->get('/verify/invalid-token');

        $response->assertRedirect('/login');
        $response->assertSessionHas('error', 'Invalid or expired verification link.');
    }

    // ==================== SEARCH COUNTRY TESTS ====================

    /** @test */
    public function it_can_search_countries()
    {
        Country::factory()->create(['name' => 'United States', 'status' => 'Enable']);
        Country::factory()->create(['name' => 'United Kingdom', 'status' => 'Enable']);
        Country::factory()->create(['name' => 'India', 'status' => 'Enable']);

        $response = $this->getJson('/search-country?type=country&keyword=United');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function it_can_search_states_by_country()
    {
        $country = Country::factory()->create(['status' => 'Enable']);
        State::factory()->create(['country_id' => $country->id, 'name' => 'California', 'status' => 'Enable']);
        State::factory()->create(['country_id' => $country->id, 'name' => 'Texas', 'status' => 'Enable']);

        $response = $this->getJson("/search-country?type=state&country_id={$country->id}");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function it_can_search_cities_by_state()
    {
        $state = State::factory()->create(['status' => 'Enable']);
        City::factory()->create(['state_id' => $state->id, 'name' => 'Los Angeles', 'status' => 'Enable']);
        City::factory()->create(['state_id' => $state->id, 'name' => 'San Francisco', 'status' => 'Enable']);

        $response = $this->getJson("/search-country?type=city&state_id={$state->id}");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    // ==================== REGISTER TESTS ====================

    /** @test */
    public function it_can_register_new_user()
    {
        Queue::fake();

        $country = Country::factory()->create(['status' => 'Enable']);
        $state = State::factory()->create(['country_id' => $country->id, 'status' => 'Enable']);
        $city = City::factory()->create(['state_id' => $state->id, 'status' => 'Enable']);

        $response = $this->postJson('/register', [
            'name' => 'Test User',
            'email' => 'newuser@example.com',
            'phone' => '1234567890',
            'company_name' => 'Test Company',
            'industry' => 'Technology',
            'country_id' => (string) $country->id,
            'state_id' => (string) $state->id,
            'city_id' => (string) $city->id,
            'pincode' => '123456',
            'address' => '123 Test Street',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Registration completed successfully. Please check your email to verify your account.',
            ]);

        Queue::assertPushed(SendRegistrationVerifyJob::class);

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'name' => 'Test User',
        ]);

        $this->assertDatabaseHas('boxes', [
            'name' => 'Test Company',
            'slug' => 'test-company',
        ]);
    }

    /** @test */
    public function it_rejects_duplicate_email_registration()
    {
        $user = User::factory()->create([
            'email' => 'existing@example.com',
            'email_verified_at' => now(),
        ]);

        $country = Country::factory()->create(['status' => 'Enable']);
        $state = State::factory()->create(['country_id' => $country->id, 'status' => 'Enable']);
        $city = City::factory()->create(['state_id' => $state->id, 'status' => 'Enable']);

        $response = $this->postJson('/register', [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'phone' => '1234567890',
            'company_name' => 'Test Company',
            'industry' => 'Technology',
            'country_id' => (string) $country->id,
            'state_id' => (string) $state->id,
            'city_id' => (string) $city->id,
            'pincode' => '123456',
            'address' => '123 Test Street',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'errors' => 'Email is already taken and verified.',
            ]);
    }

    /** @test */
    public function it_requires_all_fields_for_registration()
    {
        $response = $this->postJson('/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'email',
                'phone',
                'company_name',
                'industry',
                'country_id',
                'state_id',
                'city_id',
                'pincode',
                'address',
            ]);
    }

    // ==================== RESEND VERIFICATION TESTS ====================

    /** @test */
    public function it_can_resend_verification_email()
    {
        Queue::fake();

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'email_verified_at' => null,
        ]);

        $response = $this->postJson('/resend-verification', [
            'email' => $user->email,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Check your email inbox to verify your email, then come back here to Login.',
            ]);

        Queue::assertPushed(SendRegistrationVerifyJob::class);
    }

    /** @test */
    public function it_rejects_resending_to_already_verified_email()
    {
        $user = User::factory()->create([
            'email' => 'verified@example.com',
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/resend-verification', [
            'email' => $user->email,
        ]);

        $response->assertStatus(409)
            ->assertJson([
                'success' => false,
                'message' => 'Email is already verified.',
            ]);
    }

    // ==================== LOGIN TESTS ====================

    /** @test */
    public function it_can_login_with_valid_credentials()
    {
        $box = Box::factory()->create([
            'slug' => 'test-company',
            'working_days' => json_encode(['monday', 'tuesday', 'wednesday', 'thursday', 'friday']),
        ]);

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        BoxUser::factory()->create([
            'user_id' => $user->id,
            'box_id' => $box->id,
            'status' => 'Enabled',
        ]);

        $user->assignRole('admin');

        $response = $this->postJson('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['redirect']);

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function it_rejects_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('correct-password'),
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error' => 'Incorrect password. Please try again.',
            ]);

        $this->assertGuest();
    }

    /** @test */
    public function it_rejects_unverified_email_login()
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

        $response->assertStatus(409)
            ->assertJson([
                'success' => false,
                'resend' => true,
            ]);

        $this->assertGuest();
    }

    /** @test */
    public function it_rejects_disabled_user_login()
    {
        $box = Box::factory()->create();
        $user = User::factory()->create([
            'email' => 'disabled@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        BoxUser::factory()->create([
            'user_id' => $user->id,
            'box_id' => $box->id,
            'status' => 'Disabled',
        ]);

        $response = $this->postJson('/login', [
            'email' => 'disabled@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error' => 'Your account is currently disabled. Please contact admin.',
            ]);

        $this->assertGuest();
    }

    /** @test */
    public function it_rejects_non_existent_email()
    {
        $response = $this->postJson('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error' => 'We couldn\'t find an account with this email address.',
            ]);
    }

    // ==================== LOGOUT TESTS ====================

    /** @test */
    public function authenticated_user_can_logout()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($user);

        $response = $this->get('/logout');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
