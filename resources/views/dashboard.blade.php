<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard SIPRAS | {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/css/dashboard.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 font-sans text-slate-800 antialiased">
    @php
        $reportTotal = (int) ($counts->total ?? 0);
        $statusCards = [
            ['Total Pengaduan', $reportTotal, 'bi-inbox', 'stat-total'],
            ['Menunggu', (int) ($counts->menunggu ?? 0), 'bi-hourglass-split', 'stat-waiting'],
            ['Diproses', (int) ($counts->diproses ?? 0), 'bi-arrow-repeat', 'stat-progress'],
            ['Selesai', (int) ($counts->selesai ?? 0), 'bi-check2-circle', 'stat-done'],
        ];
        $statusRows = [
            ['Menunggu', (int) ($counts->menunggu ?? 0), 'status-waiting'],
            ['Diverifikasi', (int) ($counts->diverifikasi ?? 0), 'status-verified'],
            ['Diproses', (int) ($counts->diproses ?? 0), 'status-progress'],
            ['Selesai', (int) ($counts->selesai ?? 0), 'status-done'],
            ['Ditolak', (int) ($counts->ditolak ?? 0), 'status-rejected'],
        ];
        $statusLabels = [
            'menunggu' => 'Menunggu',
            'diverifikasi' => 'Diverifikasi',
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
        ];
        $statusClasses = [
            'menunggu' => 'badge-waiting',
            'diverifikasi' => 'badge-verified',
            'diproses' => 'badge-progress',
            'selesai' => 'badge-done',
            'ditolak' => 'badge-rejected',
        ];
    @endphp

    <div class="min-h-screen lg:pl-64">
        <aside class="dashboard-sidebar fixed inset-y-0 left-0 z-30 hidden w-64 flex-col px-5 py-6 text-white lg:flex">
            <a href="{{ route('dashboard') }}" class="mb-10 flex items-center gap-3" aria-label="SIPRAS dashboard">
                <img class="h-11 w-11 object-contain" src="{{ asset('images/ifsu-logo.png') }}" alt="">
                <span class="font-semibold leading-tight">SIPRAS<small class="mt-1 block text-[10px] font-medium tracking-wider text-slate-400">SMK INFORMATIKA</small></span>
            </a>

            <p class="sidebar-label">Menu utama</p>
            <nav class="flex flex-col gap-1" aria-label="Navigasi dashboard">
                <a href="{{ route('dashboard') }}" class="sidebar-link sidebar-link-active"><i class="bi bi-grid-1x2-fill" aria-hidden="true"></i> Dashboard</a>
                <a href="{{ route('profile.edit') }}" class="sidebar-link"><i class="bi bi-person" aria-hidden="true"></i> Profil</a>
                <a href="{{ route('home') }}" class="sidebar-link"><i class="bi bi-house" aria-hidden="true"></i> Halaman utama</a>
            </nav>

            <div class="mt-auto border-t border-white/10 pt-5">
                <p class="truncate text-sm font-semibold">{{ $user->name }}</p>
                <p class="mt-1 truncate text-xs text-slate-400">{{ $user->email }}</p>
                <form method="POST" action="{{ route('logout') }}" class="mt-4">
                    @csrf
                    <button class="sidebar-link w-full" type="submit"><i class="bi bi-box-arrow-left" aria-hidden="true"></i> Keluar</button>
                </form>
            </div>
        </aside>

        <header class="dashboard-topbar dashboard-reveal dashboard-from-top sticky top-0 z-20 flex min-h-[76px] items-center justify-between gap-4 border-b border-slate-200 bg-white px-5 sm:px-8">
            <div class="flex items-center gap-3">
                <details class="mobile-menu relative lg:hidden">
                    <summary aria-label="Buka menu navigasi"><i class="bi bi-list" aria-hidden="true"></i></summary>
                    <nav class="mobile-menu-panel absolute left-0 top-14 z-40 grid min-w-52 gap-1 rounded-lg border border-slate-200 bg-white p-2 shadow-xl" aria-label="Navigasi dashboard">
                        <a href="{{ route('dashboard') }}"><i class="bi bi-grid-1x2-fill" aria-hidden="true"></i> Dashboard</a>
                        <a href="{{ route('profile.edit') }}"><i class="bi bi-person" aria-hidden="true"></i> Profil</a>
                        <a href="{{ route('home') }}"><i class="bi bi-house" aria-hidden="true"></i> Halaman utama</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"><i class="bi bi-box-arrow-left" aria-hidden="true"></i> Keluar</button>
                        </form>
                    </nav>
                </details>
                <div>
                    <p class="text-xs font-medium text-slate-500">Ruang kerja SIPRAS</p>
                    <h1 class="text-lg font-semibold text-slate-800">Dashboard {{ $user->isSuperAdmin() ? 'Admin' : ucfirst($user->role) }}</h1>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="hidden text-sm text-slate-500 sm:block">{{ now()->translatedFormat('d F Y') }}</span>
                <div class="user-avatar" aria-hidden="true">{{ mb_substr($user->name, 0, 1) }}</div>
                <span class="hidden max-w-40 truncate text-sm font-semibold text-slate-700 md:block">{{ $user->name }}</span>
            </div>
        </header>

        <main class="dashboard-content mx-auto max-w-[1440px] space-y-6 px-5 py-7 sm:px-8" aria-label="Konten dashboard">
            <section class="dashboard-intro dashboard-reveal dashboard-from-left flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="section-eyebrow">Ringkasan SIPRAS</p>
                    <h2 class="mt-1 text-2xl font-semibold tracking-tight text-slate-800">Pantau kondisi fasilitas sekolah</h2>
                    <p class="mt-1 text-sm text-slate-600">Ringkasan pengaduan dan tindak lanjut terbaru.</p>
                </div>
                <a href="#laporan-terbaru" class="dashboard-action"><i class="bi bi-arrow-down-right" aria-hidden="true"></i> Lihat laporan terbaru</a>
            </section>

            <section class="grid gap-4 sm:grid-cols-2 2xl:grid-cols-4" aria-label="Ringkasan jumlah pengaduan">
                @foreach ($statusCards as $index => $card)
                    <article class="stat-card dashboard-reveal {{ ['dashboard-from-left', 'dashboard-from-top', 'dashboard-from-bottom', 'dashboard-from-right'][$index] }}">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-medium text-slate-500">{{ $card[0] }}</p>
                                <strong class="mt-3 block text-3xl font-semibold tracking-tight text-slate-800">{{ $card[1] }}</strong>
                            </div>
                            <span class="stat-icon {{ $card[3] }}"><i class="bi {{ $card[2] }}" aria-hidden="true"></i></span>
                        </div>
                        <p class="mt-4 flex items-center gap-2 border-t border-slate-100 pt-3 text-xs text-slate-500"><span class="status-dot {{ $card[3] }}"></span> Terdata di sistem</p>
                    </article>
                @endforeach
            </section>

            <div class="grid gap-6 xl:grid-cols-[minmax(0,1.6fr)_minmax(280px,.8fr)]">
                <section class="dashboard-panel dashboard-reveal dashboard-from-left" aria-labelledby="recent-title" id="laporan-terbaru">
                    <div class="panel-heading">
                        <div>
                            <p class="section-eyebrow">Aktivitas terbaru</p>
                            <h2 class="mt-1 text-lg font-semibold text-slate-800" id="recent-title">Laporan pengaduan</h2>
                        </div>
                        <span class="panel-counter">{{ $recentReports->count() }} laporan</span>
                    </div>
                    <div class="table-scroll overflow-x-auto" role="region" tabindex="0" aria-label="Laporan terbaru. Geser mendatar untuk melihat semua kolom.">
                        <table class="report-table w-full min-w-[620px] text-left">
                            <caption class="sr-only">Laporan pengaduan terbaru, nama pelapor, lokasi, dan status.</caption>
                            <thead><tr><th scope="col">Pengaduan</th><th scope="col">Pelapor</th><th scope="col">Lokasi</th><th scope="col">Status</th></tr></thead>
                            <tbody>
                                @forelse ($recentReports as $report)
                                    <tr>
                                        <td><span class="block font-semibold text-slate-700">{{ $report->judul }}</span><span class="mt-1 block text-xs text-slate-500">{{ $report->nomor }} &middot; {{ \Illuminate\Support\Carbon::parse($report->created_at)->translatedFormat('d M Y') }}</span></td>
                                        <td>{{ $report->reporter_name }}</td>
                                        <td>{{ $report->lokasi }}</td>
                                        <td><span class="status-badge {{ $statusClasses[$report->status] ?? 'badge-waiting' }}">{{ $statusLabels[$report->status] ?? ucfirst($report->status) }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td class="py-10 text-center text-sm text-slate-500" colspan="4">Belum ada pengaduan yang tercatat.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="dashboard-panel dashboard-reveal dashboard-from-right" aria-labelledby="status-title">
                    <div class="panel-heading">
                        <div>
                            <p class="section-eyebrow">Distribusi</p>
                            <h2 class="mt-1 text-lg font-semibold text-slate-800" id="status-title">Status pengaduan</h2>
                        </div>
                        <i class="bi bi-bar-chart-line text-lg text-slate-400" aria-hidden="true"></i>
                    </div>
                    <div class="mt-6 grid gap-5">
                        @foreach ($statusRows as $index => $row)
                            @php($percentage = $reportTotal > 0 ? (int) round(($row[1] / $reportTotal) * 100) : 0)
                            <div class="status-row dashboard-reveal {{ $index % 2 === 0 ? 'dashboard-from-right' : 'dashboard-from-bottom' }}">
                                <div class="mb-2 flex items-center justify-between text-sm"><span class="font-medium text-slate-600">{{ $row[0] }}</span><span class="font-semibold text-slate-800">{{ $row[1] }} <span class="ml-1 font-normal text-slate-600">({{ $percentage }}%)</span></span></div>
                                <div class="status-track"><div class="status-fill {{ $row[2] }}" style="width: {{ $percentage }}%"></div></div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-7 rounded-lg bg-slate-50 p-4">
                        <p class="text-sm font-semibold text-slate-700">{{ $totalUsers ?? 'Akun sekolah' }}{{ $totalUsers !== null ? ' pengguna terdaftar' : '' }}</p>
                        <p class="mt-1 text-xs leading-5 text-slate-600">SIPRAS mencatat laporan dan perubahan status dalam satu ruang kerja.</p>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
