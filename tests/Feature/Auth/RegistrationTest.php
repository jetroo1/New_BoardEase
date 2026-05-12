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

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'tenant',
        ]);

        $response->assertRedirect(route('verification.code', absolute: false));
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);

        $code = session('email_verification_code.testing_code');
        $this->assertNotEmpty($code);

        $response = $this->post('/email/verify-code', [
            'code' => $code,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/search');
        $this->assertTrue(User::where('email', 'test@example.com')->first()->hasVerifiedEmail());
    }
}
