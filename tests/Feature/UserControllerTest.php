<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guru_student_page_uses_guru_layout_and_exposes_student_actions(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $student = User::factory()->create(['role' => 'user', 'name' => 'Siswa Uji']);

        $this->actingAs($guru)
            ->get(route('guru.students.index'))
            ->assertViewIs('guru.students.index')
            ->assertSeeText('Siswa Uji')
            ->assertSee(route('guru.students.create'))
            ->assertSee(route('guru.students.edit', $student));
    }

    public function test_admin_teacher_and_student_pages_offer_the_correct_impersonation_action(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $guru = User::factory()->create(['role' => 'guru']);
        $student = User::factory()->create(['role' => 'user']);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertSee(route('impersonate', $guru));

        $this->get(route('admin.students.index'))
            ->assertSee(route('impersonate', $student));
    }

    public function test_admin_teacher_list_does_not_mix_in_students_or_admins(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        User::factory()->create(['role' => 'guru', 'name' => 'Guru Tampil']);
        User::factory()->create(['role' => 'user', 'name' => 'Siswa Tidak Tampil']);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertSeeText('Guru Tampil')
            ->assertDontSeeText('Siswa Tidak Tampil');
    }

    public function test_admin_can_create_a_teacher_profile_and_activity_record(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Guru Uji',
                'email' => 'guru.uji@example.test',
                'nip' => '19990101001',
                'major' => 'Matematika',
                'position' => 'Wali Kelas',
                'phone' => '081234567890',
                'password' => 'password-aman',
            ])
            ->assertRedirect(route('admin.users.index'));

        $teacher = User::where('email', 'guru.uji@example.test')->firstOrFail();
        $this->assertSame('guru', $teacher->role);
        $this->assertSame('19990101001', $teacher->nip);
        $this->assertSame('Matematika', $teacher->major);
        $this->assertSame('Wali Kelas', $teacher->position);
        $this->assertDatabaseHas('aktivitass', [
            'user_id' => $admin->id,
            'aktivitas' => 'Menambahkan Guru: Guru Uji',
            'referensi' => (string) $teacher->id,
        ]);
        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $admin->id,
            'judul' => 'Guru baru',
        ]);
    }

    public function test_teacher_can_create_a_student_with_school_identifiers_and_activity(): void
    {
        $teacher = User::factory()->create(['role' => 'guru']);

        $this->actingAs($teacher)
            ->post(route('guru.students.store'), [
                'name' => 'Siswa Uji',
                'email' => 'siswa.uji@example.test',
                'nis' => '24001',
                'nisn' => '0099912345',
                'class' => 'X RPL',
                'major' => 'Rekayasa Perangkat Lunak',
                'phone' => '081234567890',
                'password' => 'password-aman',
            ])
            ->assertRedirect(route('guru.students.index'));

        $student = User::where('email', 'siswa.uji@example.test')->firstOrFail();
        $this->assertSame('user', $student->role);
        $this->assertSame('24001', $student->nis);
        $this->assertSame('0099912345', $student->nisn);
        $this->assertDatabaseHas('aktivitass', [
            'user_id' => $teacher->id,
            'aktivitas' => 'Menambahkan Siswa: Siswa Uji',
            'referensi' => (string) $student->id,
        ]);
        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $teacher->id,
            'judul' => 'Siswa baru',
        ]);
    }

    public function test_admin_student_list_exposes_detail_edit_status_reset_and_impersonation_actions(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $student = User::factory()->create(['role' => 'user', 'name' => 'Siswa Lengkap']);

        $this->actingAs($admin)
            ->get(route('admin.students.index'))
            ->assertSeeText('Siswa Lengkap')
            ->assertSee(route('admin.students.show', $student))
            ->assertSee(route('admin.students.edit', $student))
            ->assertSee(route('admin.students.toggle-status', $student))
            ->assertSee(route('admin.students.reset-password', $student))
            ->assertSee(route('impersonate', $student));
    }

    public function test_guru_can_toggle_student_status_but_cannot_change_user_roles(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $student = User::factory()->create(['role' => 'user', 'status' => 'aktif']);

        $this->actingAs($guru)
            ->post(route('guru.students.toggle-status', $student))
            ->assertRedirect();

        $this->assertSame('nonaktif', $student->fresh()->status);

        $this->actingAs($guru)
            ->put(route('guru.students.update', $student), [
                'name' => 'Siswa Diperbarui',
                'email' => 'siswa.diperbarui@example.test',
                'nis' => '24002',
                'class' => 'XI RPL',
                'major' => 'RPL',
                'phone' => '081234567891',
                'role' => 'super_admin',
            ])
            ->assertRedirect(route('guru.students.index'));

        $this->assertSame('Siswa Diperbarui', $student->fresh()->name);
        $this->assertSame('siswa.diperbarui@example.test', $student->fresh()->email);
        $this->assertSame('XI RPL', $student->fresh()->class);
        $this->assertSame('user', $student->fresh()->role);
    }

    public function test_admin_can_impersonate_a_student_and_return_from_the_student_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $student = User::factory()->create(['role' => 'user']);

        $this->actingAs($admin)
            ->post(route('impersonate', $student))
            ->assertRedirect(route('user.dashboard'));

        $this->assertDatabaseHas('aktivitass', [
            'user_id' => $admin->id,
            'aktivitas' => 'Super Admin masuk sebagai Siswa: '.$student->name,
            'referensi' => (string) $student->id,
        ]);
        $this->assertAuthenticatedAs($student);
        $this->get(route('user.dashboard'))->assertSeeText('Kembali ke Super Admin');

        $this->post(route('leave-impersonation'))
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_admin_can_impersonate_a_guru_and_return_from_the_guru_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $guru = User::factory()->create(['role' => 'guru']);

        $this->actingAs($admin)
            ->post(route('impersonate', $guru))
            ->assertRedirect(route('guru.dashboard'));

        $this->assertAuthenticatedAs($guru);
        $this->get(route('guru.dashboard'))->assertSeeText('Kembali ke Super Admin');

        $this->post(route('leave-impersonation'))
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_admin_password_reset_sends_a_reset_link_without_changing_the_password(): void
    {
        Notification::fake();
        $admin = User::factory()->create(['role' => 'super_admin']);
        $user = User::factory()->create([
            'role' => 'guru',
            'password' => bcrypt('original-password'),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.users.reset-password', $user))
            ->assertRedirect();

        Notification::assertSentTo($user, ResetPassword::class);
        $this->assertTrue(password_verify('original-password', $user->fresh()->password));
    }

    public function test_guru_cannot_delete_a_student_with_complaint_history(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $student = User::factory()->create(['role' => 'user']);
        $complaintId = DB::table('pengaduans')->insertGetId([
            'user_id' => $student->id,
            'nomor' => 'HISTORY-001',
            'judul' => 'Pengaduan harus dipertahankan',
            'lokasi' => 'Ruang kelas X',
            'deskripsi' => 'Data laporan siswa.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($guru)
            ->from(route('guru.students.index'))
            ->delete(route('guru.students.destroy', $student))
            ->assertRedirect(route('guru.students.index'))
            ->assertSessionHas('error');

        $this->assertModelExists($student->fresh());
        $this->assertDatabaseHas('pengaduans', ['id' => $complaintId]);
    }

    public function test_deactivated_account_is_logged_out_on_its_next_authenticated_request(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $user = User::factory()->create(['role' => 'user', 'status' => 'aktif']);

        $this->actingAs($admin)
            ->post(route('admin.students.toggle-status', $user))
            ->assertRedirect();

        $user->refresh();

        $this->actingAs($user)
            ->get(route('profile.edit'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_legacy_inactive_status_is_logged_out_on_its_next_authenticated_request(): void
    {
        $user = User::factory()->create(['role' => 'user', 'status' => 'inactive']);

        $this->actingAs($user)
            ->get(route('profile.edit'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
