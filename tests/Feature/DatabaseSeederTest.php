<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeding_preserves_existing_users_and_creates_requested_super_admin(): void
    {
        $existingUser = User::factory()->create();

        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertModelExists($existingUser->fresh());

        $superAdmin = User::where('email', 'ikhwan@gmail.com')->firstOrFail();

        $this->assertSame('super_admin', $superAdmin->role);
        $this->assertTrue(Hash::check('1234567890', $superAdmin->password));
        $this->assertSame(1, User::where('email', 'ikhwan@gmail.com')->count());
        $this->assertMatchesRegularExpression('/^SP-[0-9a-f-]{36}$/', DB::table('pengaduans')->value('nomor'));

        $this->post(route('login'), [
            'email' => 'ikhwan@gmail.com',
            'password' => '1234567890',
        ])->assertRedirect(route('admin.dashboard', absolute: false));

        $this->assertAuthenticatedAs($superAdmin);
    }
}
