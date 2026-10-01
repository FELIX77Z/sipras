<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacilityControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guru_can_read_facility_list_and_details_but_cannot_open_admin_edit(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $category = Category::create(['nama' => 'Fasilitas']);
        $facility = Facility::create([
            'kategori_id' => $category->id,
            'nama' => 'Proyektor',
            'kode' => 'PRO-001',
            'lokasi' => 'Ruang kelas',
            'kondisi' => 'baik',
            'jumlah' => 2,
            'status' => 'aktif',
        ]);

        $this->actingAs($guru)
            ->get(route('guru.facilities.index'))
            ->assertViewIs('admin.facilities.index')
            ->assertSeeText('Proyektor')
            ->assertSee(route('guru.facilities.show', $facility))
            ->assertDontSee(route('admin.facilities.edit', $facility));

        $this->get(route('guru.facilities.show', $facility))
            ->assertViewIs('admin.facilities.show')
            ->assertSeeText('PRO-001');
    }

    public function test_admin_cannot_delete_a_facility_with_complaint_history(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $student = User::factory()->create(['role' => 'user']);
        $category = Category::create(['nama' => 'Fasilitas']);
        $facility = Facility::create([
            'kategori_id' => $category->id,
            'nama' => 'Proyektor',
            'kode' => 'PRO-002',
            'lokasi' => 'Ruang kelas',
            'kondisi' => 'baik',
            'jumlah' => 1,
            'status' => 'aktif',
        ]);
        \App\Models\Complaint::create([
            'user_id' => $student->id,
            'kategori_id' => $category->id,
            'sarana_id' => $facility->id,
            'nomor' => 'FACILITY-001',
            'judul' => 'Proyektor mati',
            'lokasi' => 'Ruang kelas',
            'deskripsi' => 'Rusak.',
            'status' => 'menunggu',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.facilities.index'))
            ->delete(route('admin.facilities.destroy', $facility))
            ->assertRedirect(route('admin.facilities.index'))
            ->assertSessionHas('error');

        $this->assertModelExists($facility->fresh());
    }
}
