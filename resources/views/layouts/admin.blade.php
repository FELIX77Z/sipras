<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIPRAS - Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    @vite(['resources/css/dashboard.css'])
</head>
<body class="dashboard-page">
    <div class="container-fluid p-0">
        <div class="row g-0 min-vh-100">
            <aside class="offcanvas-lg offcanvas-start col-lg-2 dashboard-sidebar p-3" tabindex="-1" id="adminSidebar" aria-labelledby="adminSidebarTitle">
                <div class="offcanvas-header d-lg-none px-0 pt-0">
                    <h2 class="h5 text-white mb-0" id="adminSidebarTitle">Menu SIPRAS</h2>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Tutup menu"></button>
                </div>
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <div class="fw-bold text-white fs-4">SIPRAS</div>
                        <small class="text-light">SMK Informatika Sumedang</small>
                    </div>
                </div>

                <nav class="nav flex-column gap-1">
                    <a class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'sidebar-link-active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-gauge me-2"></i>Dashboard</a>
                    <a class="sidebar-link {{ request()->routeIs('admin.complaints.*') ? 'sidebar-link-active' : '' }}" href="{{ route('admin.complaints.index') }}"><i class="fa-solid fa-file-signature me-2"></i>Pengaduan</a>
                    <a class="sidebar-link {{ request()->routeIs('admin.facilities.*') ? 'sidebar-link-active' : '' }}" href="{{ route('admin.facilities.index') }}"><i class="fa-solid fa-building-columns me-2"></i>Sarana & Prasarana</a>
                    <a class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'sidebar-link-active' : '' }}" href="{{ route('admin.categories.index') }}"><i class="fa-solid fa-tags me-2"></i>Kategori</a>
                    <a class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'sidebar-link-active' : '' }}" href="{{ route('admin.users.index') }}"><i class="fa-solid fa-user-tie me-2"></i>Data Guru</a>
                    <a class="sidebar-link {{ request()->routeIs('admin.students.*') ? 'sidebar-link-active' : '' }}" href="{{ route('admin.students.index') }}"><i class="fa-solid fa-user-graduate me-2"></i>Data Siswa</a>
                    <a class="sidebar-link {{ request()->routeIs('admin.technicians.*') ? 'sidebar-link-active' : '' }}" href="{{ route('admin.technicians.index') }}"><i class="fa-solid fa-screwdriver-wrench me-2"></i>Tim Teknisi</a>
                    <a class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'sidebar-link-active' : '' }}" href="{{ route('admin.reports.index') }}"><i class="fa-solid fa-chart-column me-2"></i>Laporan</a>
                    <a class="sidebar-link {{ request()->routeIs('admin.activities.*') ? 'sidebar-link-active' : '' }}" href="{{ route('admin.activities.index') }}"><i class="fa-solid fa-clock-rotate-left me-2"></i>Aktivitas Sistem</a>
                    <a class="sidebar-link {{ request()->routeIs('profile.*') ? 'sidebar-link-active' : '' }}" href="{{ route('profile.edit') }}"><i class="fa-solid fa-user me-2"></i>Profil</a>
                    <form action="{{ route('logout') }}" method="POST" class="mt-2">
                        @csrf
                        <button class="sidebar-link w-100 border-0 bg-transparent" type="submit"><i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>Logout</button>
                    </form>
                </nav>
            </aside>

            <main class="col-lg-10 dashboard-main">
                <nav class="dashboard-topbar navbar navbar-expand-lg px-4 py-3">
                    <div class="container-fluid">
                        <button class="btn btn-dark d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" aria-controls="adminSidebar" aria-label="Buka menu"><i class="fa-solid fa-bars" aria-hidden="true"></i></button>
                        <span class="fw-bold fs-5">SIPRAS IFSU</span>
                        <div class="ms-auto d-flex align-items-center gap-3">
                            <span class="badge bg-dark text-white rounded-pill">{{ Auth::user()->displayRole() }}</span>
                            <span class="fw-semibold">{{ Auth::user()->name }}</span>
                            <x-notifications-dropdown />
                            @if (session('super_admin_original_id'))
                                <form action="{{ route('leave-impersonation') }}" method="POST">
                                    @csrf
                                    <button class="btn btn-dark btn-sm">Kembali ke Super Admin</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </nav>

                <div class="dashboard-content">
                    @if (session('super_admin_original_id'))
                        <div class="alert alert-info" role="status">Anda sedang masuk sebagai {{ Auth::user()->displayRole() }}: {{ Auth::user()->name }}.</div>
                    @endif
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                    @include('components.dashboard-footer')
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
