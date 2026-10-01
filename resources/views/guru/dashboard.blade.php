@extends('layouts.guru')

@section('title', 'Dashboard Guru')

@section('content')
<section class="dashboard-intro mb-4" aria-labelledby="guru-dashboard-title">
    <div>
        <p class="dashboard-eyebrow mb-2">Ruang kerja Guru</p>
        <h1 id="guru-dashboard-title">Tinjau dan tindak lanjuti</h1>
        <p class="dashboard-intro-copy mb-0">Periksa laporan yang perlu perhatian, pantau progres, dan bantu siswa melaporkan fasilitas.</p>
    </div>
    <div class="dashboard-shortcuts">
        <a class="btn btn-dark" href="{{ route('guru.complaints.index', ['status' => 'menunggu']) }}"><i class="fa-solid fa-inbox me-2" aria-hidden="true"></i>Laporan baru ({{ $stats['baru'] }})</a>
        <a class="btn btn-outline-dark" href="{{ route('guru.technicians.index') }}"><i class="fa-solid fa-screwdriver-wrench me-2" aria-hidden="true"></i>Tim Teknisi</a>
        <a class="btn btn-outline-dark" href="{{ route('guru.students.create') }}"><i class="fa-solid fa-user-plus me-2" aria-hidden="true"></i>Tambah siswa</a>
        <a class="btn btn-outline-dark" href="{{ route('guru.reports.index', ['print' => 1]) }}" target="_blank" rel="noopener noreferrer"><i class="fa-solid fa-print me-2" aria-hidden="true"></i>Cetak laporan</a>
    </div>
</section>

<div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3 mb-4">
    @foreach ([['Total Teknisi', $stats['totalTeknisi'], 'fa-screwdriver-wrench'], ['Total Siswa', $stats['totalSiswa'], 'fa-user-graduate'], ['Total Pengaduan', $stats['totalPengaduan'], 'fa-file-lines'], ['Diajukan', $stats['baru'], 'fa-inbox'], ['Diverifikasi', $stats['diverifikasi'], 'fa-clipboard-check'], ['Diproses', $stats['diproses'], 'fa-screwdriver-wrench'], ['Selesai', $stats['selesai'], 'fa-circle-check'], ['Ditolak', $stats['ditolak'], 'fa-circle-xmark']] as [$label, $value, $icon])
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
    <div class="col-lg-7"><div class="card shadow-sm border-0 h-100"><div class="card-header"><h2 class="h6 mb-0">Pengaduan per Bulan</h2></div><div class="card-body"><canvas id="guruMonthlyChart" aria-label="Grafik pengaduan per bulan" role="img"></canvas></div></div></div>
    <div class="col-lg-5"><div class="card shadow-sm border-0 h-100"><div class="card-header"><h2 class="h6 mb-0">Status Pengaduan</h2></div><div class="card-body"><canvas id="guruStatusChart" aria-label="Grafik status pengaduan" role="img"></canvas></div></div></div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div><h2 class="h5 mb-1">Riwayat Pengaduan Terbaru</h2><p class="small text-muted mb-0">Semua status pengaduan sekolah ditampilkan di sini.</p></div>
        <a href="{{ route('guru.complaints.index') }}" class="btn btn-sm btn-primary">Lihat semua</a>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th>Judul</th>
                    <th>Pelapor</th>
                    <th>Status</th>
                    <th>Lokasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($complaints as $complaint)
                    <tr>
                        <td>{{ $complaint->judul }}</td>
                        <td>{{ $complaint->user->name ?? '-' }}</td>
                        <td><span class="badge {{ ['menunggu' => 'bg-warning text-dark', 'diverifikasi' => 'bg-info text-dark', 'diproses' => 'bg-primary', 'selesai' => 'bg-success', 'ditolak' => 'bg-danger'][$complaint->status] ?? 'bg-secondary' }}">{{ $complaint->statusLabel() }}</span></td>
                        <td>{{ $complaint->lokasi }}</td>
                        <td><a class="btn btn-sm btn-outline-primary" href="{{ route('complaints.show', $complaint) }}">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada pengaduan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>
<script>
    const guruCharts = @json($charts);
    new Chart(document.getElementById('guruMonthlyChart'), { type: 'line', data: { labels: guruCharts.monthly.labels, datasets: [{ label: 'Pengaduan', data: guruCharts.monthly.values, borderColor: '#0e7490', backgroundColor: 'rgba(14, 116, 144, .14)', fill: true, tension: .25 }] }, options: { responsive: true } });
    new Chart(document.getElementById('guruStatusChart'), { type: 'doughnut', data: { labels: guruCharts.statuses.labels, datasets: [{ data: guruCharts.statuses.values, backgroundColor: ['#d99d00', '#247ba0', '#70c1b3', '#f25f5c', '#50514f'] }] }, options: { responsive: true } });
</script>
@endsection
