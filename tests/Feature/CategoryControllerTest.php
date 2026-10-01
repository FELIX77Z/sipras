<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_category_details(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $category = Category::create(['nama' => 'Kelistrikan', 'deskripsi' => 'Instalasi sekolah']);

        $this->actingAs($admin)
            ->get(route('admin.categories.show', $category))
            ->assertViewIs('admin.categories.show')
            ->assertSeeText('Kelistrikan')
            ->assertSeeText('Instalasi sekolah');
    }

    public function test_admin_cannot_delete_a_category_used_by_a_complaint(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $student = User::factory()->create(['role' => 'user']);
        $category = Category::create(['nama' => 'Fasilitas']);
        \App\Models\Complaint::create([
            'user_id' => $student->id,
            'kategori_id' => $category->id,
            'nomor' => 'CATEGORY-001',
            'judul' => 'Lampu mati',
            'lokasi' => 'Kelas',
            'deskripsi' => 'Rusak.',
            'status' => 'menunggu',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.categories.index'))
            ->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('error');

        $this->assertModelExists($category->fresh());
    }
}
