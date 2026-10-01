@extends(auth()->user()->isSuperAdmin() ? 'layouts.admin' : (auth()->user()->isGuru() ? 'layouts.guru' : 'layouts.user'))

@section('title', 'Notifikasi')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h1 class="h4 mb-0">Notifikasi</h1>
        <span class="badge bg-danger">{{ $unreadNotificationCount }} belum dibaca</span>
    </div>
    <div class="list-group list-group-flush">
        @forelse ($notifications as $notification)
            <article class="list-group-item {{ $notification->dibaca_pada ? '' : 'list-group-item-warning' }}">
                <div class="d-flex justify-content-between gap-3">
                    <strong>{{ $notification->judul }}</strong>
                    <time class="small text-muted" datetime="{{ $notification->created_at->toIso8601String() }}">{{ $notification->created_at->format('d M Y H:i') }}</time>
                </div>
                <p class="mb-1 mt-2">{{ $notification->pesan }}</p>
                @if (! $notification->dibaca_pada)
                    <form action="{{ route('notifications.read', $notification) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-primary">Tandai dibaca</button>
                    </form>
                @else
                    <span class="small text-muted">Dibaca {{ $notification->dibaca_pada->format('d M Y H:i') }}</span>
                @endif
            </article>
        @empty
            <div class="p-4 text-center text-muted">Belum ada notifikasi.</div>
        @endforelse
    </div>
    <div class="card-footer">{{ $notifications->links() }}</div>
</div>
@endsection
