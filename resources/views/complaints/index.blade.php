@extends(auth()->user()->role === 'guru' ? 'layouts.guru' : (auth()->user()->role === 'super_admin' ? 'layouts.admin' : 'layouts.user'))

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Daftar Pengaduan</h4>
        @if (auth()->user()->isUser())
            <a href="{{ route('user.complaints.create') }}" class="btn btn-primary btn-sm">Buat Pengaduan</a>
        @endif
    </div>
    <form method="GET" action="{{ url()->current() }}" class="row g-2 p-3 border-bottom">
        <div class="col-md-6"><label class="visually-hidden" for="complaint-search">Cari pengaduan</label><input id="complaint-search" type="search" name="search" class="form-control" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nomor, judul, lokasi, atau pelapor"></div>
        <div class="col-md-4"><label class="visually-hidden" for="complaint-status">Filter status</label><select id="complaint-status" name="status" class="form-select"><option value="">Semua status</option>@foreach (['menunggu' => 'Menunggu', 'diverifikasi' => 'Diverifikasi', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'] as $value => $label)<option value="{{ $value }}" {{ ($filters['status'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-2 d-flex gap-2"><button class="btn btn-primary" type="submit">Terapkan</button><a href="{{ url()->current() }}" class="btn btn-outline-secondary">Reset</a></div>
    </form>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nomor</th>
                        <th>Judul</th>
                        <th>Pelapor</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($complaints as $complaint)
                        <tr>
                            <td>{{ $complaint->nomor }}</td>
                            <td>{{ $complaint->judul }}</td>
                            <td>{{ $complaint->user->name ?? '-' }}</td>
                            <td><span class="badge bg-warning text-dark">{{ $complaint->status }}</span></td>
                            <td>
                                <a href="{{ route('complaints.show', $complaint) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $complaints->links() }}</div>
    </div>
</div>
@endsection
