<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertDatabaseHas('aktivitass', [
            'user_id' => $user->id,
            'aktivitas' => 'Siswa login',
            'referensi' => 'dashboard',
        ]);
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_deactivated_users_are_logged_out_with_the_required_message(): void
    {
        $user = User::factory()->create(['status' => 'nonaktif']);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'email' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi administrator.',
            ]);

        $this->assertGuest();
    }

    public function test_login_requires_email_and_password_with_specific_messages(): void
    {
        $this->from(route('login'))
            ->post(route('login'), ['email' => '', 'password' => ''])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'email' => 'Email wajib diisi.',
                'password' => 'Password wajib diisi.',
            ]);
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('aktivitass', [
            'user_id' => $user->id,
            'aktivitas' => 'Pengguna logout',
            'referensi' => 'dashboard',
        ]);
    }
}
