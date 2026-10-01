<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Siswa')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    @vite(['resources/css/dashboard.css'])
</head>
<body class="dashboard-page">
<div class="container-fluid p-0">
    <div class="row g-0 min-vh-100">
        <aside class="offcanvas-lg offcanvas-start col-lg-2 dashboard-sidebar p-3" tabindex="-1" id="studentSidebar" aria-labelledby="studentSidebarTitle">
            <div class="offcanvas-header d-lg-none px-0 pt-0">
                <h2 class="h5 text-white mb-0" id="studentSidebarTitle">Menu SIPRAS</h2>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Tutup menu"></button>
            </div>
            <div class="text-white fw-bold fs-4 mb-4">SIPRAS</div>
            <nav class="nav flex-column gap-1">
                <a class="sidebar-link {{ request()->routeIs('user.dashboard', 'siswa.dashboard') ? 'sidebar-link-active' : '' }}" href="{{ route('user.dashboard') }}"><i class="fa-solid fa-gauge me-2"></i>Dashboard</a>
                <a class="sidebar-link {{ request()->routeIs('user.complaints.create') ? 'sidebar-link-active' : '' }}" href="{{ route('user.complaints.create') }}"><i class="fa-solid fa-plus me-2"></i>Buat Pengaduan</a>
                <a class="sidebar-link {{ request()->routeIs('user.complaints.*') && ! request()->routeIs('user.complaints.create') ? 'sidebar-link-active' : '' }}" href="{{ route('user.complaints.index') }}"><i class="fa-solid fa-clipboard-list me-2"></i>Pengaduan Saya</a>
                <a class="sidebar-link {{ request()->routeIs('profile.*') ? 'sidebar-link-active' : '' }}" href="{{ route('profile.edit') }}"><i class="fa-solid fa-user me-2"></i>Profil</a>
                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button class="sidebar-link w-100 border-0 bg-transparent" type="submit"><i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>Logout</button>
                </form>
            </nav>
        </aside>
        <main class="col-lg-10 dashboard-main">
            <nav class="dashboard-topbar navbar px-4 py-3">
                <div class="container-fluid">
                    <button class="btn btn-dark d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#studentSidebar" aria-controls="studentSidebar" aria-label="Buka menu"><i class="fa-solid fa-bars" aria-hidden="true"></i></button>
                    <span class="fw-bold">Dashboard Siswa</span>
                    <div class="ms-auto d-flex align-items-center gap-3">
                        <span class="badge bg-light text-dark">{{ Auth::user()->name }}</span>
                        <x-notifications-dropdown />
                        @if (session('super_admin_original_id'))
                            <form action="{{ route('leave-impersonation') }}" method="POST">
                                @csrf
                                <button class="btn btn-dark btn-sm" type="submit">Kembali ke Super Admin</button>
                            </form>
                        @endif
                    </div>
                </div>
            </nav>
            <div class="dashboard-content">
                @if (session('super_admin_original_id'))
                    <div class="alert alert-info" role="status">Anda sedang masuk sebagai Siswa: {{ Auth::user()->name }}.</div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
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
