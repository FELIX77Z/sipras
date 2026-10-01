@extends($layout)

@section('title', 'Laporan Pengaduan')

@section('content')
<section class="report-page-heading mb-4" aria-labelledby="reports-title">
    <div>
        <p class="dashboard-eyebrow mb-2">Rekap untuk pimpinan</p>
        <h1 id="reports-title">Laporan Pengaduan</h1>
        <p class="dashboard-intro-copy mb-0">Gunakan pencarian dan status untuk menyaring data. Cetak laporan memuat seluruh hasil yang sesuai filter.</p>
    </div>
    <div class="report-actions report-no-print">
        @if ($printMode)
            <a class="btn btn-outline-dark" href="{{ route($reportRoute, request()->except('print')) }}">Kembali ke laporan</a>
        @else
            <a class="btn btn-dark" href="{{ route($reportRoute, array_merge(request()->query(), ['print' => 1])) }}" target="_blank" rel="noopener noreferrer"><i class="fa-solid fa-print me-2" aria-hidden="true"></i>Cetak / Simpan PDF</a>
        @endif
    </div>
</section>

<div class="report-print-only report-print-header">
    <strong>SMK Informatika Sumedang</strong>
    <span>Total hasil: {{ $summary['total'] }} · SIPRAS · Dicetak {{ now()->format('d-m-Y H:i') }}</span>
</div>

<section class="report-summary row row-cols-2 row-cols-md-3 row-cols-xl-6 g-3 mb-4" aria-label="Ringkasan hasil laporan">
    @foreach ([['Total hasil', $summary['total']], ['Diajukan', $summary['menunggu']], ['Diverifikasi', $summary['diverifikasi']], ['Diproses', $summary['diproses']], ['Selesai', $summary['selesai']], ['Ditolak', $summary['ditolak']]] as [$label, $value])
        <div class="col"><div class="report-summary-item"><span>{{ $label }}</span><strong>{{ $value }}</strong></div></div>
    @endforeach
</section>

<form method="GET" action="{{ route($reportRoute) }}" class="report-controls row g-2 p-3 mb-3 bg-white border rounded-1">
    <div class="col-md-7">
        <label for="report-search" class="visually-hidden">Cari laporan</label>
        <input id="report-search" type="search" name="search" class="form-control" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nomor, judul, lokasi, pelapor">
    </div>
    <div class="col-md-3">
        <label for="report-status" class="visually-hidden">Status laporan</label>
        <select id="report-status" name="status" class="form-select">
            <option value="">Semua status</option>
            @foreach (['menunggu' => 'Diajukan', 'diverifikasi' => 'Diverifikasi', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'] as $value => $label)
                <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2 d-flex gap-2">
        <button class="btn btn-primary" type="submit">Terapkan</button>
        <a class="btn btn-outline-secondary" href="{{ route($reportRoute) }}">Reset</a>
    </div>
</form>

<div class="card shadow-sm border-0">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div><h2 class="h5 mb-1">Rincian Pengaduan</h2><p class="small text-muted mb-0">Data terbaru sesuai filter.</p></div>
        @if ($printMode)
            <span class="small text-muted">{{ $summary['total'] > 0 ? '1–'.$summary['total'].' dari '.$summary['total'] : '0–0 dari 0' }}</span>
        @else
            <span class="small text-muted">{{ $complaints->firstItem() ?? 0 }}–{{ $complaints->lastItem() ?? 0 }} dari {{ $complaints->total() }}</span>
        @endif
    </div>
    <div class="table-responsive">
        <table class="table report-table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nomor</th>
                    <th>Pelapor</th>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Sarana / Lokasi</th>
                    <th>Prioritas</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($complaints as $complaint)
                    <tr>
                        <td>{{ $complaint->nomor }}</td>
                        <td>{{ $complaint->user->name ?? '-' }}</td>
                        <td>{{ $complaint->judul }}</td>
                        <td>{{ $complaint->category->nama ?? '-' }}</td>
                        <td>{{ $complaint->facility->nama ?? $complaint->lokasi }}</td>
                        <td>{{ $complaint->priorityLabel() }}</td>
                        <td>{{ $complaint->statusLabel() }}</td>
                        <td>{{ $complaint->created_at->format('d-m-Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada pengaduan yang sesuai dengan filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @unless ($printMode)
        <div class="p-3">{{ $complaints->links() }}</div>
    @endunless
</div>

@if ($printMode)
    <script>
        window.addEventListener('load', () => window.print());
    </script>
@endif
@endsection