@extends('layouts.technician')

@section('title', 'Tugas Perbaikan')

@section('content')
<section class="dashboard-intro mb-4" aria-labelledby="technician-dashboard-title">
    <div>
        <p class="dashboard-eyebrow mb-2">Ruang kerja teknisi</p>
        <h1 id="technician-dashboard-title">Tugas Perbaikan</h1>
        <p class="dashboard-intro-copy mb-0">Lihat sarana yang ditugaskan kepada Anda dan catat progres perbaikannya.</p>
    </div>
</section>

<div class="row row-cols-2 row-cols-md-4 g-3 mb-4">
    @foreach ([['Semua tugas', $stats['total'], 'fa-clipboard-list'], ['Menunggu', $stats['menunggu'], 'fa-inbox'], ['Diproses', $stats['diproses'], 'fa-screwdriver-wrench'], ['Selesai', $stats['selesai'], 'fa-circle-check']] as [$label, $value, $icon])
        <div class="col"><article class="card dashboard-stat h-100"><div class="card-body"><div class="dashboard-stat-icon" aria-hidden="true"><i class="fa-solid {{ $icon }}"></i></div><div class="dashboard-stat-label">{{ $label }}</div><div class="dashboard-stat-value">{{ $value }}</div></div></article></div>
    @endforeach
</div>

<section class="card shadow-sm border-0" aria-labelledby="repair-list-title">
    <div class="card-header d-flex justify-content-between align-items-center gap-3">
        <div><h2 class="h5 mb-1" id="repair-list-title">Riwayat Tugas</h2><p class="small text-muted mb-0">Hanya tugas yang diberikan kepada akun Anda.</p></div>
        <span class="small text-muted">{{ $complaints->total() }} tugas</span>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light"><tr><th>Pengaduan</th><th>Pelapor</th><th>Sarana / Lokasi</th><th>Status</th><th>Catatan progres</th></tr></thead>
            <tbody>
                @forelse ($complaints as $complaint)
                    <tr>
                        <td><a href="{{ route('complaints.show', $complaint) }}"><strong>{{ $complaint->judul }}</strong></a><span class="d-block small text-muted">{{ $complaint->nomor }}</span></td>
                        <td>{{ $complaint->user->name ?? '-' }}</td>
                        <td>{{ $complaint->facility->nama ?? $complaint->lokasi }}</td>
                        <td>{{ $complaint->statusLabel() }}</td>
                        <td>
                            @if (in_array($complaint->status, ['menunggu', 'diverifikasi', 'diproses'], true))
                                <form method="POST" action="{{ route('teknisi.complaints.status', $complaint) }}" class="d-grid gap-2">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="form-select form-select-sm" aria-label="Status perbaikan untuk {{ $complaint->nomor }}" required>
                                        <option value="diproses" @selected($complaint->status === 'diproses')>Sedang diproses</option>
                                        <option value="selesai">Selesai</option>
                                    </select>
                                    <input name="isi" class="form-control form-control-sm" placeholder="Catatan perbaikan" maxlength="2000" required aria-label="Catatan progres {{ $complaint->nomor }}">
                                    <button class="btn btn-sm btn-outline-primary" type="submit">Simpan progres</button>
                                </form>
                            @else
                                <span class="text-muted">Tidak ada tindakan untuk status ini</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada tugas perbaikan untuk akun ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $complaints->links() }}</div>
</section>
@endsection
<div>
    <!-- Happiness is not something readymade. It comes from your own actions. - Dalai Lama -->
</div>
