<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_sees_only_their_notifications_and_can_mark_them_read(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $otherUser = User::factory()->create(['role' => 'user']);
        $notification = Notification::create([
            'user_id' => $user->id,
            'judul' => 'Pengaduan diproses',
            'pesan' => 'Laporan sedang ditangani.',
        ]);
        Notification::create([
            'user_id' => $otherUser->id,
            'judul' => 'Privat',
            'pesan' => 'Notifikasi pengguna lain.',
        ]);

        $this->actingAs($user)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSeeText('Pengaduan diproses')
            ->assertDontSeeText('Notifikasi pengguna lain.');

        $this->post(route('notifications.read', $notification))
            ->assertRedirect();

        $this->assertNotNull($notification->fresh()->dibaca_pada);
    }

    public function test_user_cannot_mark_another_users_notification_as_read(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $otherUser = User::factory()->create(['role' => 'user']);
        $notification = Notification::create([
            'user_id' => $otherUser->id,
            'judul' => 'Privat',
            'pesan' => 'Notifikasi pengguna lain.',
        ]);

        $this->actingAs($user)
            ->post(route('notifications.read', $notification))
            ->assertNotFound();

        $this->assertNull($notification->fresh()->dibaca_pada);
    }
}
