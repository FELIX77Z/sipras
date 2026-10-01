<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiprasAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_login_and_redirect_to_admin_dashboard(): void
    {
        $user = User::create([
            'name' => 'Ikhwan',
            'email' => 'ikhwan@gmail.com',
            'password' => bcrypt('1234567890'),
            'role' => 'super_admin',
            'status' => 'aktif',
        ]);

        $response = $this->post('/login', [
            'email' => 'ikhwan@gmail.com',
            'password' => '1234567890',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($user);
    }
}
