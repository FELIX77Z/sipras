<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TechnicianSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_development_seeder_adds_technician_categories_facilities_and_repair_history_idempotently(): void
    {
        $this->seed(DatabaseSeeder::class);
        $superAdmin = User::where('email', config('services.sipras.super_admin_email'))->firstOrFail();
        $superAdmin->forceFill(['password' => Hash::make('rotated-test-password')])->save();
        $this->seed(DatabaseSeeder::class);

        $superAdmin->refresh();
        $this->assertTrue(Hash::check('rotated-test-password', $superAdmin->password));

        $technician = User::where('email', 'teknisi@sipras.test')->firstOrFail();

        $this->assertSame('teknisi', $technician->role);
        $this->assertDatabaseHas('kategoris', ['nama' => 'Penerangan dan Listrik']);
        $this->assertDatabaseHas('saranas', ['kode' => 'EL-001', 'nama' => 'Lampu Ruang Kelas']);
        $this->assertDatabaseHas('pengaduans', [
            'nomor' => 'SP-DEMO-20261001-001',
            'judul' => 'Lampu ruang kelas mati',
            'technician_id' => $technician->id,
        ]);
        $this->assertSame(1, Category::where('nama', 'Penerangan dan Listrik')->count());
        $this->assertSame(1, Complaint::where('nomor', 'SP-DEMO-20261001-001')->count());
    }
}
