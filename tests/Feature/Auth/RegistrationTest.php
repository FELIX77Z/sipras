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

        $response->assertOk()
            ->assertSee('name="nis"', false)
            ->assertSee('name="nisn"', false)
            ->assertSee('name="class"', false)
            ->assertSee('name="major"', false)
            ->assertSee('name="phone"', false)
            ->assertSee('name="agreement"', false);
    }

    public function test_new_users_can_register(): void
    {
        User::factory()->create(['role' => 'super_admin']);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'nisn' => '1234567890',
            'email' => 'test@example.com',
            'class' => 'X RPL',
            'major' => 'Rekayasa Perangkat Lunak',
            'phone' => '081234567890',
            'password' => 'password',
            'password_confirmation' => 'password',
            'agreement' => '1',
            'role' => 'super_admin',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'nisn' => '1234567890',
            'role' => 'user',
        ]);
        $this->assertDatabaseHas('notifikasis', [
            'judul' => 'Siswa baru',
            'pesan' => 'Test User mendaftar melalui formulir publik.',
        ]);
    }

    public function test_registration_requires_school_profile_and_agreement_fields(): void
    {
        $this->from(route('register'))
            ->post(route('register'), [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors(['nis', 'nisn', 'class', 'major', 'phone', 'agreement']);

        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
    }
}
