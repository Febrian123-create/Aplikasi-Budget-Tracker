<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_new_users_can_register_and_must_verify_otp(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('verification.otp'));
        
        // Assert that registration is pending in session
        $pending = session('pending_registration');
        $this->assertNotNull($pending);
        $this->assertEquals('test@example.com', $pending['email']);
        $otpCode = $pending['otp_code'];
        $this->assertNotNull($otpCode);

        // Verify OTP
        $response = $this->post('/verify-otp', [
            'otp_code' => $otpCode,
        ]);

        $response->assertRedirect(route('dashboard'));
        
        // Now the user should be stored in the database and authenticated
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertAuthenticatedAs($user);
    }

    public function test_registration_fails_with_invalid_email_format(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => '2472022@maranatha',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertNull(session('pending_registration'));
    }
}
