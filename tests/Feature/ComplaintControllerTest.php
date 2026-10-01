<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ComplaintControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_cannot_view_another_students_complaint(): void
    {
        $student = User::factory()->create(['role' => 'user']);
        $otherStudent = User::factory()->create(['role' => 'user']);
        $complaintId = $this->createComplaint($otherStudent);

        $this->actingAs($student)
            ->get(route('complaints.show', $complaintId))
            ->assertNotFound();
    }

    public function test_student_can_search_and_filter_only_their_own_complaints(): void
    {
        $student = User::factory()->create(['role' => 'user']);
        $otherStudent = User::factory()->create(['role' => 'user']);
        DB::table('pengaduans')->insert([
            [
                'user_id' => $student->id,
                'nomor' => 'FILTER-001',
                'judul' => 'Lampu koridor',
                'lokasi' => 'Koridor',
                'deskripsi' => 'Lampu padam.',
                'status' => 'diproses',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $student->id,
                'nomor' => 'FILTER-002',
                'judul' => 'Lampu kelas',
                'lokasi' => 'Kelas',
                'deskripsi' => 'Lampu padam.',
                'status' => 'menunggu',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $otherStudent->id,
                'nomor' => 'FILTER-003',
                'judul' => 'Lampu aula',
                'lokasi' => 'Aula',
                'deskripsi' => 'Lampu padam.',
                'status' => 'diproses',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->actingAs($student)
            ->get(route('user.complaints.index', ['search' => 'Lampu', 'status' => 'diproses']))
            ->assertSeeText('Lampu koridor')
            ->assertDontSeeText('Lampu kelas')
            ->assertDontSeeText('Lampu aula')
            ->assertSee('value="Lampu"', false);
    }

    public function test_student_cannot_change_complaint_status(): void
    {
        $student = User::factory()->create(['role' => 'user']);
        $complaintId = $this->createComplaint($student);

        $this->actingAs($student)
            ->put(route('complaints.updateStatus', $complaintId), ['status' => 'selesai'])
            ->assertForbidden();

        $this->assertDatabaseHas('pengaduans', [
            'id' => $complaintId,
            'status' => 'menunggu',
        ]);
    }

    public function test_student_cannot_send_a_response_to_a_complaint(): void
    {
        $student = User::factory()->create(['role' => 'user']);
        $complaintId = $this->createComplaint($student);

        $this->actingAs($student)
            ->post(route('complaints.response', $complaintId), ['isi' => 'Sudah diperbaiki'])
            ->assertForbidden();

        $this->assertDatabaseMissing('tanggapans', [
            'pengaduan_id' => $complaintId,
        ]);
    }

    public function test_admin_can_delete_a_complaint_and_the_action_is_logged(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $complaintId = $this->createComplaint(User::factory()->create(['role' => 'user']));

        $this->actingAs($admin)
            ->from(route('admin.complaints.index'))
            ->delete(route('admin.complaints.destroy', $complaintId))
            ->assertRedirect(route('admin.complaints.index'));

        $this->assertDatabaseMissing('pengaduans', ['id' => $complaintId]);
        $this->assertDatabaseHas('aktivitass', [
            'user_id' => $admin->id,
            'aktivitas' => 'Menghapus pengaduan',
            'referensi' => (string) $complaintId,
        ]);
    }

    public function test_staff_can_mark_a_complaint_as_verified(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $complaintId = $this->createComplaint(User::factory()->create(['role' => 'user']));

        $this->actingAs($guru)
            ->put(route('complaints.updateStatus', $complaintId), ['status' => 'diverifikasi']);

        $this->assertDatabaseHas('pengaduans', [
            'id' => $complaintId,
            'status' => 'diverifikasi',
        ]);
    }

    public function test_status_change_notifies_the_reporter_and_is_always_added_to_history(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $student = User::factory()->create(['role' => 'user']);
        $complaintId = $this->createComplaint($student);

        $this->actingAs($guru)
            ->put(route('complaints.updateStatus', $complaintId), [
                'status' => 'diproses',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('tanggapans', [
            'pengaduan_id' => $complaintId,
            'user_id' => $guru->id,
            'status' => 'diproses',
        ]);
        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $student->id,
            'judul' => 'Status pengaduan diperbarui',
        ]);
    }

    public function test_staff_can_send_a_response_that_students_can_read(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $student = User::factory()->create(['role' => 'user']);
        $complaintId = $this->createComplaint($student);

        $this->actingAs($guru)
            ->get(route('complaints.show', $complaintId))
            ->assertSee(route('complaints.response', $complaintId))
            ->assertSee('Tanggapan Pengaduan');

        $this->post(route('complaints.response', $complaintId), [
            'isi' => 'Perbaikan dijadwalkan besok.',
        ])->assertRedirect();

        $this->assertDatabaseHas('tanggapans', [
            'pengaduan_id' => $complaintId,
            'user_id' => $guru->id,
            'isi' => 'Perbaikan dijadwalkan besok.',
        ]);
        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $student->id,
            'judul' => 'Tanggapan baru',
            'pesan' => 'Perbaikan dijadwalkan besok.',
        ]);
        $this->assertDatabaseHas('aktivitass', [
            'user_id' => $guru->id,
            'aktivitas' => 'Mengirim tanggapan pengaduan',
            'referensi' => (string) $complaintId,
        ]);

        $this->actingAs($student)
            ->get(route('complaints.show', $complaintId))
            ->assertSeeText('Perbaikan dijadwalkan besok.')
            ->assertDontSee('name="isi"', false);
    }

    public function test_staff_can_select_the_verified_status_on_complaint_details(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $complaintId = $this->createComplaint(User::factory()->create(['role' => 'user']));

        $this->actingAs($guru)
            ->get(route('complaints.show', $complaintId))
            ->assertSee('value="diverifikasi"', false);
    }

    public function test_only_staff_can_see_technician_contact_details_on_a_complaint(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $student = User::factory()->create(['role' => 'user']);
        $technician = User::factory()->create([
            'role' => 'teknisi',
            'phone' => '081234567890',
        ]);
        $complaintId = $this->createComplaint($student);
        DB::table('pengaduans')->where('id', $complaintId)->update(['technician_id' => $technician->id]);

        $this->actingAs($guru)
            ->get(route('complaints.show', $complaintId))
            ->assertSeeText($technician->phone)
            ->assertSee('name="technician_id"', false);

        $this->actingAs($student)
            ->get(route('complaints.show', $complaintId))
            ->assertDontSeeText($technician->phone)
            ->assertDontSeeText($technician->email);
    }

    public function test_technicians_can_only_view_complaints_assigned_to_them(): void
    {
        $student = User::factory()->create([
            'role' => 'user',
            'phone' => '089876543210',
        ]);
        $technician = User::factory()->create(['role' => 'teknisi']);
        $otherTechnician = User::factory()->create(['role' => 'teknisi']);
        $complaintId = $this->createComplaint($student);
        DB::table('pengaduans')->where('id', $complaintId)->update(['technician_id' => $technician->id]);

        $this->actingAs($otherTechnician)
            ->get(route('complaints.show', $complaintId))
            ->assertNotFound();

        $this->actingAs($technician)
            ->get(route('complaints.show', $complaintId))
            ->assertOk()
            ->assertSeeText('Kursi kelas rusak')
            ->assertDontSeeText($student->phone)
            ->assertDontSeeText($student->email);
    }

    public function test_student_can_submit_a_complaint_with_a_priority(): void
    {
        $student = User::factory()->create(['role' => 'user']);
        User::factory()->create(['role' => 'super_admin', 'status' => 'aktif']);
        User::factory()->create(['role' => 'guru', 'status' => 'aktif']);
        $category = Category::create(['nama' => 'Fasilitas']);

        $response = $this->actingAs($student)->post(route('user.complaints.store'), [
            'kategori_id' => $category->id,
            'judul' => 'Kipas ruang kelas rusak',
            'lokasi' => 'Ruang kelas X',
            'deskripsi' => 'Kipas tidak dapat dinyalakan.',
            'prioritas' => 'tinggi',
        ]);

        $response->assertRedirect(route('user.complaints.index'));
        $this->assertDatabaseHas('pengaduans', [
            'user_id' => $student->id,
            'judul' => 'Kipas ruang kelas rusak',
            'prioritas' => 'tinggi',
            'status' => 'menunggu',
        ]);
        $this->assertSame(2, Notification::whereIn('user_id', User::whereIn('role', ['super_admin', 'guru'])->pluck('id'))->count());
    }

    public function test_complaints_submitted_in_the_same_second_receive_unique_numbers(): void
    {
        $student = User::factory()->create(['role' => 'user']);
        $category = Category::create(['nama' => 'Fasilitas']);
        $payload = [
            'kategori_id' => $category->id,
            'lokasi' => 'Ruang kelas X',
            'deskripsi' => 'Perlu diperiksa.',
            'prioritas' => 'sedang',
        ];

        $this->travelTo(now());
        $firstResponse = $this->actingAs($student)->post(route('user.complaints.store'), $payload + ['judul' => 'Laporan pertama']);
        $secondResponse = $this->post(route('user.complaints.store'), $payload + ['judul' => 'Laporan kedua']);
        $this->travelBack();

        $firstResponse->assertRedirect(route('user.complaints.index'));
        $secondResponse->assertRedirect(route('user.complaints.index'));
        $this->assertSame(2, Complaint::query()->distinct('nomor')->count('nomor'));
    }

    private function createComplaint(User $user): int
    {
        return DB::table('pengaduans')->insertGetId([
            'user_id' => $user->id,
            'nomor' => 'SIPRAS-'.fake()->unique()->numerify('##########'),
            'judul' => 'Kursi kelas rusak',
            'lokasi' => 'Ruang kelas X',
            'deskripsi' => 'Kursi perlu diperbaiki.',
            'status' => 'menunggu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
