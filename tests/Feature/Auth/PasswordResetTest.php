<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordOtpMail;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');
        $response->assertStatus(200);
    }

    public function test_reset_password_otp_can_be_requested(): void
    {
        Mail::fake();

        $user = User::factory()->create();

        $response = $this->post('/forgot-password', ['email' => $user->email]);

        $response->assertRedirect(route('password.otp'));
        $user->refresh();
        $this->assertNotNull($user->reset_otp_code);

        Mail::assertSent(ResetPasswordOtpMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_reset_password_otp_screen_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->withSession(['pending_reset_email' => $user->email])
            ->get('/reset-otp');

        $response->assertStatus(200);
    }

    public function test_password_can_be_reset_with_valid_otp(): void
    {
        $user = User::factory()->create();

        
        $this->post('/forgot-password', ['email' => $user->email]);
        $user->refresh();

        
        $response = $this->withSession(['pending_reset_email' => $user->email])
            ->post('/reset-otp', [
                'otp' => $user->reset_otp_code,
            ]);

        $response->assertRedirect(route('password.reset.form'));
        $response->assertSessionHas('reset_password_allowed_email', $user->email);

        
        $response = $this->withSession([
            'reset_password_allowed_email' => $user->email,
        ])->get('/reset-password');
        $response->assertStatus(200);

        
        $response = $this->withSession([
            'reset_password_allowed_email' => $user->email,
        ])->post('/reset-password', [
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect(route('login'));
    }
}
