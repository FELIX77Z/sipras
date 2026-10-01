<div class="dropdown">
    <button class="btn btn-outline-dark position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifikasi">
        <i class="fa-solid fa-bell" aria-hidden="true"></i>
        <span class="ms-1">Notifikasi</span>
        @if ($unreadNotificationCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ $unreadNotificationCount }}</span>
        @endif
    </button>
    <div class="dropdown-menu dropdown-menu-end p-2" style="min-width: 19rem">
        <div class="d-flex justify-content-between align-items-center px-2 py-1">
            <strong>Notifikasi terbaru</strong>
            <a href="{{ route('notifications.index') }}" class="small">Lihat semua</a>
        </div>
        @forelse ($recentNotifications as $notification)
            <div class="dropdown-item-text border-top px-2 py-2 {{ $notification->dibaca_pada ? 'text-muted' : '' }}">
                <div class="d-flex justify-content-between gap-2">
                    <strong>{{ $notification->judul }}</strong>
                    @if (! $notification->dibaca_pada)
                        <span class="badge bg-warning text-dark">Baru</span>
                    @endif
                </div>
                <div class="small text-wrap">{{ $notification->pesan }}</div>
                @if (! $notification->dibaca_pada)
                    <form action="{{ route('notifications.read', $notification) }}" method="POST" class="mt-1">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-link p-0">Tandai dibaca</button>
                    </form>
                @endif
            </div>
        @empty
            <div class="dropdown-item-text text-muted">Belum ada notifikasi.</div>
        @endforelse
    </div>
</div>
