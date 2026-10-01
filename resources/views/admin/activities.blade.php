@extends('layouts.admin')

@section('title', 'Aktivitas Sistem')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header"><h4 class="mb-0">Aktivitas Sistem</h4></div>
    <div class="card-body">
        <ul class="list-group">
            @foreach($activities as $activity)
                <li class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $activity->user->name ?? 'Sistem' }}</strong>
                            <div class="text-muted small">{{ $activity->aktivitas }}</div>
                            @if ($activity->referensi)
                                <div class="small">Referensi: {{ $activity->referensi }}</div>
                            @endif
                        </div>
                        <span class="text-muted small">{{ $activity->created_at->format('d M Y H:i') }}</span>
                    </div>
                </li>
            @endforeach
        </ul>
        <div class="mt-3">{{ $activities->links() }}</div>
    </div>
</div>
@endsection
