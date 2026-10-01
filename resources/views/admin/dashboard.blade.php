@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<section class="dashboard-intro mb-4" aria-labelledby="admin-dashboard-title">
    <div>
        <p class="dashboard-eyebrow mb-2">Ruang kendali sekolah</p>
        <h1 id="admin-dashboard-title">Ringkasan SIPRAS</h1>
        <p class="dashboard-intro-copy mb-0">Pantau laporan seluruh sekolah, atur akun, lalu pastikan tindak lanjut berjalan sampai selesai.</p>
    </div>
    <div class="dashboard-shortcuts">
        <a class="btn btn-dark" href="{{ route('admin.complaints.index', ['status' => 'menunggu']) }}"><i class="fa-solid fa-inbox me-2" aria-hidden="true"></i>Periksa pengaduan baru ({{ $stats['baru'] }})</a>
        <a class="btn btn-outline-dark" href="{{ route('admin.technicians.index') }}"><i class="fa-solid fa-screwdriver-wrench me-2" aria-hidden="true"></i>Tim Teknisi</a>
        <a class="btn btn-outline-dark" href="{{ route('admin.reports.index') }}"><i class="fa-solid fa-print me-2" aria-hidden="true"></i>Buka laporan</a>
    </div>
</section>

<div class="row row-cols-2 row-cols-md-3 row-cols-xl-5 g-3 mb-4">
    @foreach ([['Total Guru', $stats['totalGuru'], 'fa-user-tie'], ['Total Teknisi', $stats['totalTeknisi'], 'fa-screwdriver-wrench'], ['Total Siswa', $stats['totalSiswa'], 'fa-user-graduate'], ['Total Pengaduan', $stats['totalPengaduan'], 'fa-file-lines'], ['Diajukan', $stats['baru'], 'fa-inbox'], ['Diverifikasi', $stats['diverifikasi'], 'fa-clipboard-check'], ['Diproses', $stats['diproses'], 'fa-screwdriver-wrench'], ['Selesai', $stats['selesai'], 'fa-circle-check'], ['Ditolak', $stats['ditolak'], 'fa-circle-xmark'], ['Total Sarana', $stats['totalSarana'], 'fa-building']] as [$label, $value, $icon])
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
    <div class="col-lg-6"><div class="card shadow-sm border-0 h-100"><div class="card-header"><h2 class="h6 mb-0">Pengaduan per Bulan</h2></div><div class="card-body"><canvas id="monthlyChart" aria-label="Grafik pengaduan per bulan" role="img"></canvas></div></div></div>
    <div class="col-lg-6"><div class="card shadow-sm border-0 h-100"><div class="card-header"><h2 class="h6 mb-0">Status Pengaduan</h2></div><div class="card-body"><canvas id="statusChart" aria-label="Grafik status pengaduan" role="img"></canvas></div></div></div>
    <div class="col-lg-6"><div class="card shadow-sm border-0 h-100"><div class="card-header"><h2 class="h6 mb-0">Pengaduan per Kategori</h2></div><div class="card-body"><canvas id="categoryChart" aria-label="Grafik pengaduan per kategori" role="img"></canvas></div></div></div>
    <div class="col-lg-6"><div class="card shadow-sm border-0 h-100"><div class="card-header"><h2 class="h6 mb-0">Pengguna</h2></div><div class="card-body"><canvas id="roleChart" aria-label="Grafik pengguna menurut role" role="img"></canvas></div></div></div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div><h2 class="h5 mb-1">Riwayat Pengaduan Terbaru</h2><p class="small text-muted mb-0">Semua status terbaru beserta pelapor, fasilitas, dan tindak lanjutnya.</p></div>
        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.complaints.index') }}">Semua pengaduan</a>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Pelapor</th>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Lokasi</th>
                    <th>Prioritas</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($complaints as $complaint)
                    <tr>
                        <td>{{ $complaint->nomor }}</td>
                        <td>{{ $complaint->user->name ?? '-' }}</td>
                        <td>{{ $complaint->judul }}</td>
                        <td>{{ $complaint->category->nama ?? '-' }}</td>
                        <td>{{ $complaint->lokasi }}</td>
                        <td>{{ ucfirst($complaint->prioritas ?? 'sedang') }}</td>
                        <td><span class="badge {{ ['menunggu' => 'bg-warning text-dark', 'diverifikasi' => 'bg-info text-dark', 'diproses' => 'bg-primary', 'selesai' => 'bg-success', 'ditolak' => 'bg-danger'][$complaint->status] ?? 'bg-secondary' }}">{{ $complaint->statusLabel() }}</span></td>
                        <td>{{ $complaint->created_at->format('d-m-Y') }}</td>
                        <td><a href="{{ route('complaints.show', $complaint) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">Belum ada pengaduan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>
<script>
    const chartData = @json($charts);
    const palette = ['#d99d00', '#247ba0', '#70c1b3', '#f25f5c', '#50514f', '#b2dbbf'];
    new Chart(document.getElementById('monthlyChart'), { type: 'line', data: { labels: chartData.monthly.labels, datasets: [{ label: 'Pengaduan', data: chartData.monthly.values, borderColor: '#247ba0', backgroundColor: 'rgba(36, 123, 160, .14)', fill: true, tension: .25 }] }, options: { responsive: true, maintainAspectRatio: true } });
    new Chart(document.getElementById('statusChart'), { type: 'doughnut', data: { labels: chartData.statuses.labels, datasets: [{ data: chartData.statuses.values, backgroundColor: palette }] }, options: { responsive: true } });
    new Chart(document.getElementById('categoryChart'), { type: 'bar', data: { labels: chartData.categories.labels, datasets: [{ label: 'Pengaduan', data: chartData.categories.values, backgroundColor: '#d99d00' }] }, options: { responsive: true, indexAxis: 'y' } });
    new Chart(document.getElementById('roleChart'), { type: 'pie', data: { labels: chartData.roles.labels, datasets: [{ data: chartData.roles.values, backgroundColor: palette }] }, options: { responsive: true } });
</script>
@endsection
