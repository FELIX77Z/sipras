@extends('layouts.user')

@section('title', 'Dashboard Siswa')

@section('content')
<section class="dashboard-intro mb-4" aria-labelledby="student-dashboard-title">
    <div>
        <p class="dashboard-eyebrow mb-2">Ruang Siswa</p>
        <h1 id="student-dashboard-title">Pantau pengaduanmu</h1>
        <p class="dashboard-intro-copy mb-0">Buat laporan fasilitas dan ikuti setiap pembaruan status dari halaman ini.</p>
    </div>
    <div class="dashboard-shortcuts">
        <a class="btn btn-dark" href="{{ route('user.complaints.create') }}"><i class="fa-solid fa-plus me-2" aria-hidden="true"></i>Buat pengaduan</a>
        <a class="btn btn-outline-dark" href="{{ route('user.complaints.index') }}"><i class="fa-solid fa-clock-rotate-left me-2" aria-hidden="true"></i>Riwayat saya</a>
    </div>
</section>

<div class="row row-cols-2 row-cols-md-3 row-cols-xl-6 g-3 mb-4">
    @foreach ([['Total Pengaduan', $stats['total'], 'fa-file-lines'], ['Diajukan', $stats['diajukan'], 'fa-inbox'], ['Diverifikasi', $stats['diverifikasi'], 'fa-clipboard-check'], ['Diproses', $stats['diproses'], 'fa-screwdriver-wrench'], ['Selesai', $stats['selesai'], 'fa-circle-check'], ['Ditolak', $stats['ditolak'], 'fa-circle-xmark']] as [$label, $value, $icon])
        <div class="col">
            <article class="card dashboard-stat h-100">
                <div class="card-body">
                    <div class="dashboard-stat-icon" aria-hidden="true"><i class="fa-solid {{ $icon }}"></i></div>
                    <div class="dashboard-stat-label">{{ $label }}</div>
                    <div class="dashboard-stat-value">{{ $value }}</div>
                </div>
            </article>
        </div>
    @endforeach
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-7"><div class="card shadow-sm border-0 h-100"><div class="card-header"><h2 class="h6 mb-0">Pengaduan per Bulan</h2></div><div class="card-body"><canvas id="studentMonthlyChart" aria-label="Grafik pengaduan per bulan" role="img"></canvas></div></div></div>
    <div class="col-lg-5"><div class="card shadow-sm border-0 h-100"><div class="card-header"><h2 class="h6 mb-0">Status Pengaduan</h2></div><div class="card-body"><canvas id="studentStatusChart" aria-label="Grafik status pengaduan" role="img"></canvas></div></div></div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div><h2 class="h5 mb-1">Pengaduan terbaru</h2><p class="small text-muted mb-0">Status dan catatan perkembangan laporan milikmu.</p></div>
        <a href="{{ route('user.complaints.index') }}" class="btn btn-sm btn-outline-primary">Buka riwayat</a>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th>Judul</th>
                    <th>Status</th>
                    <th>Lokasi</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($complaints as $complaint)
                    <tr>
                        <td><a href="{{ route('user.complaints.show', $complaint) }}">{{ $complaint->judul }}</a></td>
                        <td><span class="badge {{ ['menunggu' => 'bg-warning text-dark', 'diverifikasi' => 'bg-info text-dark', 'diproses' => 'bg-primary', 'selesai' => 'bg-success', 'ditolak' => 'bg-danger'][$complaint->status] ?? 'bg-secondary' }}">{{ $complaint->statusLabel() }}</span></td>
                        <td>{{ $complaint->lokasi }}</td>
                        <td>{{ $complaint->created_at->format('d-m-Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">Belum ada pengaduan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>
<script>
    const studentCharts = @json($charts);
    new Chart(document.getElementById('studentMonthlyChart'), { type: 'line', data: { labels: studentCharts.monthly.labels, datasets: [{ label: 'Pengaduan', data: studentCharts.monthly.values, borderColor: '#247ba0', backgroundColor: 'rgba(36, 123, 160, .14)', fill: true, tension: .25 }] }, options: { responsive: true } });
    new Chart(document.getElementById('studentStatusChart'), { type: 'doughnut', data: { labels: studentCharts.statuses.labels, datasets: [{ data: studentCharts.statuses.values, backgroundColor: ['#d99d00', '#247ba0', '#70c1b3', '#f25f5c', '#50514f'] }] }, options: { responsive: true } });
</script>
@endsection
