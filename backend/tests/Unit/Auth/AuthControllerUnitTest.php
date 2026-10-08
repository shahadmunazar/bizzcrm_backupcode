<?php

namespace Tests\Unit\Auth;

use App\Http\Controllers\Auth\AuthController;
use App\Jobs\DepartmentCreateJob;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Session;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthControllerUnitTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected AuthController $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->controller = new AuthController();

        // Create roles
        Role::create(['name' => 'superadmin']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'manager']);
        Role::create(['name' => 'employee']);
        Role::create(['name' => 'leads']);
        Role::create(['name' => 'deals']);
        Role::create(['name' => 'project']);
        Role::create(['name' => 'project-manager']);
        Role::create(['name' => 'tracking']);

        Queue::fake();
    }
    
    /**
     * Test showLoginForm returns correct view
     */
    #[Test]
    public function test_show_login_form_returns_view()
    {
        $response = $this->controller->showLoginForm();

        $this->assertEquals('auth.login', $response->name());
    }

    /**
     * Test forget_password returns correct view
     */
    #[Test]
    public function test_forget_password_returns_view()
    {
        $response = $this->controller->forget_password();

        $this->assertEquals('auth.forget', $response->name());
    }

    /**
     * Test showRegisterForm returns correct view
     */
    #[Test]
    public function test_show_register_form_returns_view()
    {
        $response = $this->controller->showRegisterForm();

        $this->assertEquals('auth.register', $response->name());
    }

    /**
     * Test reset_page returns correct view
     */
    #[Test]
    public function test_reset_page_returns_view()
    {
        $response = $this->controller->reset_page();

        $this->assertEquals('auth.reset_passowrd', $response->name());
    }

    /**
     * Test login_mobile returns correct view
     */
    #[Test]
    public function test_login_mobile_returns_view()
    {
        $response = $this->controller->login_mobile();

        $this->assertEquals('auth.mobile', $response->name());
    }

    /**
     * Test sendOtp with valid email
     */
    #[Test]
    public function test_send_otp_with_valid_email()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'email_verified_at' => now(),
        ]);

        $request = Request::create('/send-otp', 'POST', [
            'user_input' => 'test@example.com',
        ]);

        $response = $this->controller->sendOtp($request);
        $data = $response->getData(true);

        $this->assertEquals(200, $response->status());
        $this->assertTrue($data['success']);
        $this->assertEquals('OTP sent successfully and valid for 10 minutes.', $data['message']);

        $user->refresh();
        $this->assertNotNull($user->otp);
        $this->assertNotNull($user->otp_expires_at);

        Queue::assertPushed(SendOtpJob::class);
    }

    /**
     * Test sendOtp with valid mobile
     *
     */
    #[Test]
    public function test_send_otp_with_valid_mobile()
    {
        $user = User::factory()->create([
            'mobile' => '1234567890',
            'email' => 'test@example.com',
            'email_verified_at' => now(),
        ]);

        $request = Request::create('/send-otp', 'POST', [
            'user_input' => '1234567890',
        ]);

        $response = $this->controller->sendOtp($request);
        $data = $response->getData(true);

        $this->assertEquals(200, $response->status());
        $this->assertTrue($data['success']);

        Queue::assertPushed(SendOtpJob::class);
    }

    /**
     * Test sendOtp with unverified email
     *
     */
    #[Test]
    public function test_send_otp_with_unverified_email()
    {
        $user = User::factory()->create([
            'email' => 'unverified@example.com',
            'email_verified_at' => null,
        ]);

        $request = Request::create('/send-otp', 'POST', [
            'user_input' => 'unverified@example.com',
        ]);

        $response = $this->controller->sendOtp($request);
        $data = $response->getData(true);

        $this->assertEquals(403, $response->status());
        $this->assertFalse($data['success']);
    }

    /**
     * Test sendOtp with nonexistent user
     *
     */
    #[Test]
    public function test_send_otp_with_nonexistent_user()
    {
        $request = Request::create('/send-otp', 'POST', [
            'user_input' => 'nonexistent@example.com',
        ]);

        $response = $this->controller->sendOtp($request);

        $this->assertEquals(404, $response->status());
    }

    /**
     * Test sendOtp without user_input
     *
     */
    #[Test]
    public function test_send_otp_without_user_input()
    {
        $request = Request::create('/send-otp', 'POST', []);

        $response = $this->controller->sendOtp($request);

        $this->assertEquals(422, $response->status());
    }

    /**
     * Test changePassword with valid data
     *
     */
    #[Test]
    public function test_change_password_with_valid_data()
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword'),
        ]);

        $this->actingAs($user);

        $request = Request::create('/change-password', 'POST', [
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);

        $response = $this->controller->changePassword($request);
        $data = $response->getData(true);

        $this->assertEquals(200, $data['status']);
        $this->assertEquals('Password changed successfully.', $data['message']);

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    /**
     * Test changePassword with mismatched confirmation
     *
     */
    #[Test]
    public function test_change_password_with_mismatched_confirmation()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $request = Request::create('/change-password', 'POST', [
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'differentpassword123',
        ]);

        $response = $this->controller->changePassword($request);

        $this->assertEquals(403, $response->status());
    }

    /**
     * Test changePassword with short password
     *
     */
    #[Test]
    public function test_change_password_with_short_password()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $request = Request::create('/change-password', 'POST', [
            'new_password' => 'short',
            'new_password_confirmation' => 'short',
        ]);

        $response = $this->controller->changePassword($request);

        $this->assertEquals(403, $response->status());
    }

    /**
     * Test verifyOtp with correct OTP
     *
     */
    #[Test]
    public function test_verify_otp_with_correct_otp()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'otp' => '123456',
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        $box = Box::factory()->create();
        BoxUser::factory()->create([
            'user_id' => $user->id,
            'box_id' => $box->id,
            'status' => 'Enabled',
        ]);

        $user->assignRole('admin');

        $request = Request::create('/verify-otp', 'POST', [
            'user_input' => 'test@example.com',
            'otp' => '123456',
        ]);

        $response = $this->controller->verifyOtp($request);
        $data = $response->getData(true);

        $this->assertEquals(200, $response->status());
        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('redirect_to', $data);
    }

    /**
     * Test verifyOtp with incorrect OTP
     *
     */
    #[Test]
    public function test_verify_otp_with_incorrect_otp()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'otp' => '123456',
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        $request = Request::create('/verify-otp', 'POST', [
            'user_input' => 'test@example.com',
            'otp' => '999999',
        ]);

        $response = $this->controller->verifyOtp($request);
        $data = $response->getData(true);

        $this->assertEquals(422, $response->status());
        $this->assertFalse($data['success']);
        $this->assertEquals('invalid_otp', $data['type']);
    }

    /**
     * Test verifyOtp with expired OTP
     *
     */
    #[Test]
    public function test_verify_otp_with_expired_otp()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'otp' => '123456',
            'otp_expires_at' => Carbon::now()->subMinutes(5),
        ]);

        $request = Request::create('/verify-otp', 'POST', [
            'user_input' => 'test@example.com',
            'otp' => '123456',
        ]);

        $response = $this->controller->verifyOtp($request);
        $data = $response->getData(true);

        $this->assertEquals(410, $response->status());
        $this->assertFalse($data['success']);
        $this->assertEquals('expired_otp', $data['type']);
    }

    /**
     * Test verifyOtp with nonexistent user
     *
     */
    #[Test]
    public function test_verify_otp_with_nonexistent_user()
    {
        $request = Request::create('/verify-otp', 'POST', [
            'user_input' => 'nonexistent@example.com',
            'otp' => '123456',
        ]);

        $response = $this->controller->verifyOtp($request);
        $data = $response->getData(true);

        $this->assertEquals(404, $response->status());
        $this->assertFalse($data['success']);
        $this->assertEquals('not_found', $data['type']);
    }

    /**
     * Test Reset_Password with valid token
     *
     */
    #[Test]
    public function test_reset_password_with_valid_token()
    {
        $token = 'valid-reset-token';
        $user = User::factory()->create([
            'remember_token' => $token,
            'password' => Hash::make('oldpassword'),
        ]);

        $request = Request::create('/reset-password', 'POST', [
            'token' => $token,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response = $this->controller->Reset_Password($request);
        $data = $response->getData(true);

        $this->assertEquals(200, $response->status());
        $this->assertTrue($data['success']);
        $this->assertEquals('Password reset successful!', $data['message']);

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
        $this->assertNull($user->remember_token);
    }

    /**
     * Test Reset_Password with invalid token
     *
     */
    #[Test]
    public function test_reset_password_with_invalid_token()
    {
        $request = Request::create('/reset-password', 'POST', [
            'token' => 'invalid-token',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response = $this->controller->Reset_Password($request);
        $data = $response->getData(true);

        $this->assertEquals(422, $response->status());
        $this->assertFalse($data['success']);
        $this->assertEquals('Invalid or expired token.', $data['message']);
    }

    /**
     * Test Reset_Password with missing token
     *
     */
    #[Test]
    public function test_reset_password_with_missing_token()
    {
        $request = Request::create('/reset-password', 'POST', [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response = $this->controller->Reset_Password($request);

        $this->assertEquals(422, $response->status());
    }

    /**
     * Test forget_Email with valid email
     *
     */
    #[Test]
    public function test_forget_email_with_valid_email()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'email_verified_at' => now(),
        ]);

        $request = Request::create('/forget-email', 'POST', [
            'email' => 'test@example.com',
        ]);

        $response = $this->controller->forget_Email($request);
        $data = $response->getData(true);

        $this->assertEquals(200, $response->status());
        $this->assertTrue($data['success']);

        $user->refresh();
        $this->assertNotNull($user->remember_token);

        Queue::assertPushed(SendResetPasswordEmailJob::class);
    }

    /**
     * Test forget_Email with unverified email
     *
     */
    #[Test]
    public function test_forget_email_with_unverified_email()
    {
        $user = User::factory()->create([
            'email' => 'unverified@example.com',
            'email_verified_at' => null,
        ]);

        $request = Request::create('/forget-email', 'POST', [
            'email' => 'unverified@example.com',
        ]);

        $response = $this->controller->forget_Email($request);
        $data = $response->getData(true);

        $this->assertEquals(403, $response->status());
        $this->assertFalse($data['success']);
    }

    /**
     * Test forget_Email with nonexistent email
     *
     */
    #[Test]
    public function test_forget_email_with_nonexistent_email()
    {
        $request = Request::create('/forget-email', 'POST', [
            'email' => 'nonexistent@example.com',
        ]);

        $response = $this->controller->forget_Email($request);
        $data = $response->getData(true);

        // Should return success to avoid user enumeration
        $this->assertEquals(200, $response->status());
        $this->assertTrue($data['success']);
    }

    /**
     * Test forget_Email with invalid email format
     *
     */
    #[Test]
    public function test_forget_email_with_invalid_email_format()
    {
        $request = Request::create('/forget-email', 'POST', [
            'email' => 'not-an-email',
        ]);

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $this->controller->forget_Email($request);
    }

    /**
     * Test verify with valid token
     *
     */
    #[Test]
    public function test_verify_with_valid_token()
    {
        $token = 'verification-token';
        $user = User::factory()->create([
            'remember_token' => $token,
            'email_verified_at' => null,
        ]);

        $box = Box::factory()->create();
        $boxUser = BoxUser::factory()->create([
            'user_id' => $user->id,
            'box_id' => $box->id,
            'status' => 'Disabled',
        ]);

        $response = $this->controller->verify($token);

        $this->assertEquals(302, $response->status());
        $this->assertTrue($response->isRedirect('/login'));

        $user->refresh();
        $boxUser->refresh();

        $this->assertNotNull($user->email_verified_at);
        $this->assertEquals('Enabled', $boxUser->status);
    }

    /**
     * Test verify with invalid token
     *
     */
    #[Test]
    public function test_verify_with_invalid_token()
    {
        $response = $this->controller->verify('invalid-token');

        $this->assertEquals(302, $response->status());
        $this->assertTrue($response->isRedirect('/login'));
    }

    /**
     * Test verify with already verified email
     *
     */
    #[Test]
    public function test_verify_with_already_verified_email()
    {
        $token = 'verification-token';
        $user = User::factory()->create([
            'remember_token' => $token,
            'email_verified_at' => now(),
        ]);

        $box = Box::factory()->create();
        BoxUser::factory()->create([
            'user_id' => $user->id,
            'box_id' => $box->id,
            'status' => 'Enabled',
        ]);

        $response = $this->controller->verify($token);

        $this->assertEquals(302, $response->status());
        $this->assertTrue($response->isRedirect('/login'));
    }

    /**
     * Test SearchCountry for countries
     *
     */
    #[Test]
    public function test_search_country_returns_countries()
    {
        Country::create(['name' => 'India', 'status' => 'Enable']);
        Country::create(['name' => 'Indonesia', 'status' => 'Enable']);
        Country::create(['name' => 'USA', 'status' => 'Enable']);

        $request = Request::create('/search-country', 'GET', [
            'type' => 'country',
            'keyword' => 'ind',
        ]);

        $response = $this->controller->SearchCountry($request);
        $data = $response->getData(true);

        $this->assertEquals(200, $response->status());
        $this->assertArrayHasKey('data', $data);
        $this->assertGreaterThanOrEqual(2, count($data['data']));
    }

    /**
     * Test SearchCountry for states
     *
     */
    #[Test]
    public function test_search_country_returns_states()
    {
        $country = Country::create(['name' => 'India', 'status' => 'Enable']);
        State::create(['name' => 'Gujarat', 'country_id' => $country->id, 'status' => 'Enable']);
        State::create(['name' => 'Maharashtra', 'country_id' => $country->id, 'status' => 'Enable']);

        $request = Request::create('/search-country', 'GET', [
            'type' => 'state',
            'country_id' => $country->id,
        ]);

        $response = $this->controller->SearchCountry($request);
        $data = $response->getData(true);

        $this->assertEquals(200, $response->status());
        $this->assertArrayHasKey('data', $data);
        $this->assertEquals(2, count($data['data']));
    }

    /**
     * Test SearchCountry for states without country_id
     *
     */
    #[Test]
    public function test_search_country_states_without_country_id()
    {
        $request = Request::create('/search-country', 'GET', [
            'type' => 'state',
        ]);

        $response = $this->controller->SearchCountry($request);
        $data = $response->getData(true);

        $this->assertEquals(400, $response->status());
        $this->assertEquals('country_id required', $data['error']);
    }

    /**
     * Test SearchCountry for cities
     *
     */
    #[Test]
    public function test_search_country_returns_cities()
    {
        $country = Country::create(['name' => 'India', 'status' => 'Enable']);
        $state = State::create(['name' => 'Gujarat', 'country_id' => $country->id, 'status' => 'Enable']);
        City::create(['name' => 'Ahmedabad', 'state_id' => $state->id, 'status' => 'Enable']);
        City::create(['name' => 'Surat', 'state_id' => $state->id, 'status' => 'Enable']);

        $request = Request::create('/search-country', 'GET', [
            'type' => 'city',
            'state_id' => $state->id,
        ]);

        $response = $this->controller->SearchCountry($request);
        $data = $response->getData(true);

        $this->assertEquals(200, $response->status());
        $this->assertArrayHasKey('data', $data);
        $this->assertEquals(2, count($data['data']));
    }

    /**
     * Test SearchCountry for cities without state_id
     *
     */
    #[Test]
    public function test_search_country_cities_without_state_id()
    {
        $request = Request::create('/search-country', 'GET', [
            'type' => 'city',
        ]);

        $response = $this->controller->SearchCountry($request);
        $data = $response->getData(true);

        $this->assertEquals(400, $response->status());
        $this->assertEquals('state_id required', $data['error']);
    }

    /**
     * Test SearchCountry with invalid type
     *
     */
    #[Test]
    public function test_search_country_with_invalid_type()
    {
        $request = Request::create('/search-country', 'GET', [
            'type' => 'invalid',
        ]);

        $response = $this->controller->SearchCountry($request);
        $data = $response->getData(true);

        $this->assertEquals(400, $response->status());
        $this->assertEquals('Invalid type', $data['error']);
    }

    /**
     * Test register with valid data
     *
     */
    #[Test]
    public function test_register_with_valid_data()
    {
        $registrationData = [
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

        $request = Request::create('/register', 'POST', $registrationData);

        $response = $this->controller->register($request);
        $data = $response->getData(true);

        $this->assertEquals(200, $response->status());
        $this->assertTrue($data['success']);

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

    /**
     * Test register with duplicate email
     *
     */
    #[Test]
    public function test_register_with_duplicate_verified_email()
    {
        User::factory()->create([
            'email' => 'duplicate@example.com',
            'email_verified_at' => now(),
        ]);

        $registrationData = [
            'name' => 'John Doe',
            'email' => 'duplicate@example.com',
            'phone' => '1234567890',
            'company_name' => 'Test Company',
            'industry' => 'Technology',
            'country_id' => '101',
            'state_id' => '1',
            'city_id' => '1',
            'pincode' => '123456',
            'address' => '123 Test Street',
        ];

        $request = Request::create('/register', 'POST', $registrationData);

        $response = $this->controller->register($request);
        $data = $response->getData(true);

        $this->assertEquals(422, $response->status());
        $this->assertFalse($data['success']);
    }

    /**
     * Test register with unverified email
     *
     */
    #[Test]
    public function test_register_with_duplicate_unverified_email()
    {
        User::factory()->create([
            'email' => 'unverified@example.com',
            'email_verified_at' => null,
        ]);

        $registrationData = [
            'name' => 'John Doe',
            'email' => 'unverified@example.com',
            'phone' => '1234567890',
            'company_name' => 'Test Company',
            'industry' => 'Technology',
            'country_id' => '101',
            'state_id' => '1',
            'city_id' => '1',
            'pincode' => '123456',
            'address' => '123 Test Street',
        ];

        $request = Request::create('/register', 'POST', $registrationData);

        $response = $this->controller->register($request);
        $data = $response->getData(true);

        $this->assertEquals(422, $response->status());
        $this->assertEquals(409, $data['status']);
        $this->assertFalse($data['success']);
    }

    /**
     * Test register with duplicate company name
     *
     */
    #[Test]
    public function test_register_with_duplicate_company_name()
    {
        $user = User::factory()->create();
        Box::factory()->create([
            'name' => 'Existing Company',
            'slug' => 'existing-company',
            'superadmin_id' => $user->id,
        ]);

        $registrationData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'company_name' => 'Existing Company',
            'industry' => 'Technology',
            'country_id' => '101',
            'state_id' => '1',
            'city_id' => '1',
            'pincode' => '123456',
            'address' => '123 Test Street',
        ];

        $request = Request::create('/register', 'POST', $registrationData);

        $response = $this->controller->register($request);
        $data = $response->getData(true);

        $this->assertEquals(422, $response->status());
        $this->assertFalse($data['success']);
    }

    /**
     * Test register with missing required fields
     *
     */
    #[Test]
    public function test_register_with_missing_required_fields()
    {
        $request = Request::create('/register', 'POST', [
            'name' => 'John Doe',
        ]);

        $response = $this->controller->register($request);
        $data = $response->getData(true);

        $this->assertEquals(422, $response->status());
        $this->assertFalse($data['success']);
        $this->assertArrayHasKey('errors', $data);
    }

    /**
     * Test resendVerificationEmail with valid email
     *
     */
    #[Test]
    public function test_resend_verification_email_with_valid_email()
    {
        User::factory()->create([
            'email' => 'unverified@example.com',
            'email_verified_at' => null,
        ]);

        $request = Request::create('/resend-verification', 'POST', [
            'email' => 'unverified@example.com',
        ]);

        $response = $this->controller->resendVerificationEmail($request);
        $data = $response->getData(true);

        $this->assertEquals(200, $response->status());
        $this->assertTrue($data['success']);

        Queue::assertPushed(SendRegistrationVerifyJob::class);
    }

    /**
     * Test resendVerificationEmail with already verified email
     *
     */
    #[Test]
    public function test_resend_verification_email_with_verified_email()
    {
        User::factory()->create([
            'email' => 'verified@example.com',
            'email_verified_at' => now(),
        ]);

        $request = Request::create('/resend-verification', 'POST', [
            'email' => 'verified@example.com',
        ]);

        $response = $this->controller->resendVerificationEmail($request);
        $data = $response->getData(true);

        $this->assertEquals(409, $response->status());
        $this->assertFalse($data['success']);
        $this->assertEquals('Email is already verified.', $data['message']);
    }

    /**
     * Test resendVerificationEmail with nonexistent email
     *
     */
    #[Test]
    public function test_resend_verification_email_with_nonexistent_email()
    {
        $request = Request::create('/resend-verification', 'POST', [
            'email' => 'nonexistent@example.com',
        ]);

        $response = $this->controller->resendVerificationEmail($request);

        $this->assertEquals(422, $response->status());
    }

    /**
     * Test login with valid credentials
     *
     */
    #[Test]
    public function test_login_with_valid_credentials()
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

        $user->assignRole('admin');

        $request = Request::create('/login', 'POST', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response = $this->controller->login($request);
        $data = $response->getData(true);

        $this->assertEquals(200, $response->status());
        $this->assertArrayHasKey('redirect', $data);
    }

    /**
     * Test login with wrong password
     *
     */
    #[Test]
    public function test_login_with_wrong_password()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $request = Request::create('/login', 'POST', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response = $this->controller->login($request);
        $data = $response->getData(true);

        $this->assertEquals(422, $response->status());
        $this->assertFalse($data['success']);
        $this->assertEquals('Incorrect password. Please try again.', $data['error']);
    }

    /**
     * Test login with nonexistent email
     *
     */
    #[Test]
    public function test_login_with_nonexistent_email()
    {
        $request = Request::create('/login', 'POST', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $response = $this->controller->login($request);
        $data = $response->getData(true);

        $this->assertEquals(422, $response->status());
        $this->assertFalse($data['success']);
    }

    /**
     * Test login with unverified email
     *
     */
    #[Test]
    public function test_login_with_unverified_email()
    {
        User::factory()->create([
            'email' => 'unverified@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => null,
        ]);

        $request = Request::create('/login', 'POST', [
            'email' => 'unverified@example.com',
            'password' => 'password123',
        ]);

        $response = $this->controller->login($request);
        $data = $response->getData(true);

        $this->assertEquals(409, $response->status());
        $this->assertFalse($data['success']);
        $this->assertTrue($data['resend']);
    }

    /**
     * Test login with disabled account
     *
     */
    #[Test]
    public function test_login_with_disabled_account()
    {
        $user = User::factory()->create([
            'email' => 'disabled@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $box = Box::factory()->create();
        BoxUser::factory()->create([
            'user_id' => $user->id,
            'box_id' => $box->id,
            'status' => 'Disabled',
        ]);

        $request = Request::create('/login', 'POST', [
            'email' => 'disabled@example.com',
            'password' => 'password123',
        ]);

        $response = $this->controller->login($request);
        $data = $response->getData(true);

        $this->assertEquals(422, $response->status());
        $this->assertFalse($data['success']);
    }

    /**
     * Test login without BoxUser record
     *
     */
    #[Test]
    public function test_login_without_box_user_record()
    {
        User::factory()->create([
            'email' => 'nobox@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $request = Request::create('/login', 'POST', [
            'email' => 'nobox@example.com',
            'password' => 'password123',
        ]);

        $response = $this->controller->login($request);
        $data = $response->getData(true);

        $this->assertEquals(422, $response->status());
        $this->assertFalse($data['success']);
    }

    /**
     * Test logout
     *
     */
    #[Test]
    public function test_logout()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($user);

        $request = Request::create('/logout', 'POST', [
            'password' => 'password123',
        ]);

        Session::start();
        $request->setLaravelSession(Session::getFacadeRoot());

        $response = $this->controller->logout($request);

        $this->assertEquals(302, $response->status());
        $this->assertTrue($response->isRedirect(route('login')));
        $this->assertGuest();
    }

    /**
     * Test all role redirects after successful login
     *
     */
    #[Test]
    public function test_login_redirects_for_different_roles()
    {
        $roles = ['superadmin', 'admin', 'manager', 'employee', 'leads', 'deals', 'project', 'tracking'];

        foreach ($roles as $roleName) {
            // Create fresh user for each role
            $user = User::factory()->create([
                'email' => "{$roleName}@example.com",
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]);

            $box = Box::factory()->create(['slug' => 'test-company']);
            BoxUser::factory()->create([
                'user_id' => $user->id,
                'box_id' => $box->id,
                'status' => 'Enabled',
            ]);

            $user->assignRole($roleName);

            Auth::logout(); // Logout previous user

            $request = Request::create('/login', 'POST', [
                'email' => "{$roleName}@example.com",
                'password' => 'password123',
            ]);

            $response = $this->controller->login($request);
            $data = $response->getData(true);

            $this->assertEquals(200, $response->status(), "Failed for role: {$roleName}");
            $this->assertArrayHasKey('redirect', $data);
            $this->assertStringContainsString('test-company', $data['redirect']);
        }
    }
}

