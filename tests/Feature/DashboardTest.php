<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_shows_reports_from_all_users(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $reporter = User::factory()->create(['role' => 'siswa']);
        $this->createReport($reporter, 'SIPRAS-ADMIN-001', 'Laporan seluruh sekolah');
        $this->createReport($admin, 'SIPRAS-ADMIN-002', 'Laporan admin', 'diproses');
        $this->createReport($reporter, 'SIPRAS-ADMIN-003', 'Laporan diverifikasi', 'diverifikasi');
        $this->createReport($reporter, 'SIPRAS-ADMIN-004', 'Laporan ditolak', 'ditolak');
        $this->createReport($reporter, 'SIPRAS-ADMIN-005', 'Laporan selesai', 'selesai');

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertSeeText('Laporan seluruh sekolah')
            ->assertViewHas('counts', fn ($counts): bool => (int) $counts->total === 5
                && (int) $counts->menunggu === 1
                && (int) $counts->diverifikasi === 1
                && (int) $counts->diproses === 1
                && (int) $counts->selesai === 1
                && (int) $counts->ditolak === 1);
    }

    public function test_student_dashboard_only_shows_their_own_reports(): void
    {
        $student = User::factory()->create(['role' => 'siswa']);
        $otherStudent = User::factory()->create(['role' => 'siswa']);
        $this->createReport($student, 'SIPRAS-STUDENT-001', 'Laporan milik saya');
        $this->createReport($otherStudent, 'SIPRAS-STUDENT-002', 'Laporan milik orang lain', 'selesai');

        $this->actingAs($student)
            ->get(route('siswa.dashboard'))
            ->assertSeeText('Laporan milik saya')
            ->assertDontSeeText('Laporan milik orang lain')
            ->assertViewHas('counts', fn ($counts): bool => (int) $counts->total === 1
                && (int) $counts->menunggu === 1
                && (int) $counts->diverifikasi === 0
                && (int) $counts->selesai === 0
                && (int) $counts->ditolak === 0);
    }

    public function test_guru_dashboard_includes_every_complaint_status_in_its_summary(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $student = User::factory()->create(['role' => 'siswa']);
        $this->createReport($student, 'SIPRAS-GURU-001', 'Laporan menunggu', 'menunggu');
        $this->createReport($student, 'SIPRAS-GURU-002', 'Laporan diverifikasi', 'diverifikasi');
        $this->createReport($student, 'SIPRAS-GURU-003', 'Laporan diproses', 'diproses');
        $this->createReport($student, 'SIPRAS-GURU-004', 'Laporan selesai', 'selesai');
        $this->createReport($student, 'SIPRAS-GURU-005', 'Laporan ditolak', 'ditolak');

        $this->actingAs($guru)
            ->get(route('guru.dashboard'))
            ->assertSeeText('Laporan selesai')
            ->assertViewHas('stats', fn (array $stats): bool => $stats['baru'] === 1
                && $stats['diverifikasi'] === 1
                && $stats['diproses'] === 1
                && $stats['selesai'] === 1
                && $stats['ditolak'] === 1);
    }

    public function test_admin_dashboard_builds_chart_datasets_from_database_records(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $student = User::factory()->create(['role' => 'user']);
        $category = Category::create(['nama' => 'Laboratorium']);
        $this->createReport($student, 'SIPRAS-CHART-001', 'Laporan grafik', 'diproses', $category->id);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertViewHas('charts', fn (array $charts): bool => in_array('Laboratorium', $charts['categories']['labels'], true)
                && in_array('Siswa', $charts['roles']['labels'], true)
                && $charts['statuses']['values'][array_search('Diproses', $charts['statuses']['labels'], true)] === 1
                && $charts['monthly']['values'][5] === 1);
    }

    public function test_admin_print_report_includes_all_filtered_rows_not_only_the_current_page(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $student = User::factory()->create(['role' => 'siswa']);

        foreach (range(1, 21) as $number) {
            DB::table('pengaduans')->insert([
                'user_id' => $student->id,
                'nomor' => sprintf('PRINT-%03d', $number),
                'judul' => 'Laporan cetak '.$number,
                'lokasi' => 'Ruang kelas',
                'deskripsi' => 'Ringkasan laporan.',
                'status' => 'menunggu',
                'created_at' => now()->subMinutes(21 - $number),
                'updated_at' => now(),
            ]);
        }

        $this->actingAs($admin)
            ->get(route('admin.reports.index', ['print' => 1, 'status' => 'menunggu']))
            ->assertOk()
            ->assertSeeText('PRINT-001')
            ->assertSeeText('PRINT-021')
            ->assertSeeText('Total hasil: 21');
    }

    public function test_admin_print_report_shows_the_result_range_with_report_details(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $student = User::factory()->create(['role' => 'siswa']);
        $this->createReport($student, 'SIPRAS-PRINT-001', 'Kursi laboratorium rusak');
        $this->createReport($student, 'SIPRAS-PRINT-002', 'Laporan selesai', 'selesai');

        $this->actingAs($admin)
            ->get(route('admin.reports.index', ['print' => 1, 'status' => 'menunggu']))
            ->assertOk()
            ->assertSeeText('Rincian Pengaduan')
            ->assertSeeText('Data terbaru sesuai filter.')
            ->assertSeeText('1–1 dari 1')
            ->assertSeeText('SIPRAS-PRINT-001')
            ->assertSeeText('Kursi laboratorium rusak')
            ->assertSeeText('Ruang kelas X')
            ->assertDontSeeText('SIPRAS-PRINT-002')
            ->assertDontSeeText('Laporan selesai');
    }

    public function test_guru_report_uses_guru_layout_and_print_route(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);

        $this->actingAs($guru)
            ->get(route('guru.reports.index'))
            ->assertViewHas('layout', 'layouts.guru')
            ->assertSeeText('Guru Dashboard')
            ->assertDontSeeText('SIPRAS IFSU')
            ->assertSee(route('guru.reports.index', ['print' => 1]));
    }

    public function test_technician_accounts_have_a_dedicated_dashboard(): void
    {
        $technician = User::factory()->create(['role' => 'teknisi']);

        $this->actingAs($technician)
            ->get('/teknisi/dashboard')
            ->assertOk()
            ->assertSeeText('Tugas Perbaikan');
    }

    public function test_only_admin_and_guru_can_view_technician_contacts(): void
    {
        $technician = User::factory()->create([
            'role' => 'teknisi',
            'phone' => '081234567890',
        ]);

        $this->actingAs(User::factory()->create(['role' => 'super_admin']))
            ->get('/admin/technicians')
            ->assertOk()
            ->assertSeeText($technician->phone);

        $this->actingAs(User::factory()->create(['role' => 'guru']))
            ->get('/guru/technicians')
            ->assertOk()
            ->assertSeeText($technician->phone);

        $this->actingAs(User::factory()->create(['role' => 'user']))
            ->get('/admin/technicians')
            ->assertForbidden();
    }

    public function test_staff_can_create_technician_accounts_but_students_cannot(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $payload = [
            'name' => 'Teknisi Gedung A',
            'email' => 'teknisi.gedung-a@example.test',
            'phone' => '081234567891',
            'position' => 'Teknisi Listrik',
            'password' => 'safe-password-123',
            'password_confirmation' => 'safe-password-123',
        ];

        $this->actingAs($admin)
            ->post(route('admin.technicians.store'), $payload)
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', [
            'email' => 'teknisi.gedung-a@example.test',
            'role' => 'teknisi',
            'phone' => '081234567891',
        ]);

        $this->actingAs(User::factory()->create(['role' => 'user']))
            ->post(route('admin.technicians.store'), $payload)
            ->assertForbidden();
    }

    public function test_staff_can_assign_a_complaint_and_only_its_technician_can_update_it(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $technician = User::factory()->create(['role' => 'teknisi']);
        $otherTechnician = User::factory()->create(['role' => 'teknisi']);
        $student = User::factory()->create(['role' => 'user']);
        $this->createReport($student, 'SIPRAS-TECH-001', 'Kursi laboratorium rusak');
        $complaintId = DB::table('pengaduans')->where('nomor', 'SIPRAS-TECH-001')->value('id');

        $this->actingAs($admin)
            ->patch(route('admin.complaints.technician', $complaintId), ['technician_id' => $technician->id])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('pengaduans', [
            'id' => $complaintId,
            'technician_id' => $technician->id,
        ]);

        $this->actingAs($otherTechnician)
            ->put('/teknisi/complaints/'.$complaintId.'/status', ['status' => 'selesai', 'isi' => 'Sudah diperbaiki.'])
            ->assertNotFound();

        $this->actingAs($technician)
            ->put('/teknisi/complaints/'.$complaintId.'/status', ['status' => 'diproses', 'isi' => 'Perbaikan dimulai.'])
            ->assertRedirect();

        $this->assertDatabaseHas('pengaduans', [
            'id' => $complaintId,
            'status' => 'diproses',
            'technician_id' => $technician->id,
        ]);
        $this->assertDatabaseHas('tanggapans', [
            'pengaduan_id' => $complaintId,
            'user_id' => $technician->id,
            'isi' => 'Perbaikan dimulai.',
        ]);

        $this->put(route('teknisi.complaints.status', $complaintId), [
            'status' => 'selesai',
            'isi' => 'Kursi sudah diperbaiki.',
        ])->assertRedirect();

        $this->put(route('teknisi.complaints.status', $complaintId), [
            'status' => 'diproses',
            'isi' => 'Mencoba membuka ulang tugas.',
        ])->assertNotFound();

        $this->assertDatabaseHas('pengaduans', [
            'id' => $complaintId,
            'status' => 'selesai',
        ]);

        $this->actingAs($technician)
            ->get(route('teknisi.dashboard'))
            ->assertSeeText('Kursi laboratorium rusak');
    }

    public function test_assigned_technician_cannot_reopen_a_rejected_complaint(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $technician = User::factory()->create(['role' => 'teknisi']);
        $student = User::factory()->create(['role' => 'user']);
        $this->createReport($student, 'SIPRAS-TECH-REJECTED', 'Laporan ditolak', 'ditolak');
        $complaintId = DB::table('pengaduans')->where('nomor', 'SIPRAS-TECH-REJECTED')->value('id');
        DB::table('pengaduans')->where('id', $complaintId)->update(['technician_id' => $technician->id]);

        $this->actingAs($admin)
            ->patch(route('admin.complaints.technician', $complaintId), ['technician_id' => $technician->id])
            ->assertNotFound();

        $this->actingAs($technician)
            ->put(route('teknisi.complaints.status', $complaintId), [
                'status' => 'diproses',
                'isi' => 'Mencoba membuka laporan ditolak.',
            ])
            ->assertNotFound();

        $this->assertDatabaseHas('pengaduans', [
            'id' => $complaintId,
            'status' => 'ditolak',
        ]);

        $this->actingAs($technician)
            ->get(route('teknisi.dashboard'))
            ->assertSeeText('Laporan ditolak')
            ->assertDontSeeText('Simpan progres');
    }

    private function createReport(User $user, string $number, string $title, string $status = 'menunggu', ?int $categoryId = null): void
    {
        DB::table('pengaduans')->insert([
            'user_id' => $user->id,
            'kategori_id' => $categoryId,
            'nomor' => $number,
            'judul' => $title,
            'lokasi' => 'Ruang kelas X',
            'deskripsi' => 'Fasilitas perlu diperiksa.',
            'status' => $status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
