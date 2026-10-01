<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Teknisi')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    @vite(['resources/css/dashboard.css'])
</head>
<body class="dashboard-page">
<div class="container-fluid p-0">
    <div class="row g-0 min-vh-100">
        <aside class="offcanvas-lg offcanvas-start col-lg-2 dashboard-sidebar p-3" tabindex="-1" id="technicianSidebar" aria-labelledby="technicianSidebarTitle">
            <div class="offcanvas-header d-lg-none px-0 pt-0">
                <h2 class="h5 text-white mb-0" id="technicianSidebarTitle">Menu SIPRAS</h2>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Tutup menu"></button>
            </div>
            <div class="text-white fw-bold fs-4 mb-4">SIPRAS <small class="d-block text-light fs-6">Teknisi</small></div>
            <nav class="nav flex-column gap-1" aria-label="Navigasi teknisi">
                <a class="sidebar-link sidebar-link-active" href="{{ route('teknisi.dashboard') }}"><i class="fa-solid fa-gauge me-2" aria-hidden="true"></i>Tugas Perbaikan</a>
                <a class="sidebar-link" href="{{ route('profile.edit') }}"><i class="fa-solid fa-user me-2" aria-hidden="true"></i>Profil</a>
                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button class="sidebar-link w-100 border-0 bg-transparent" type="submit"><i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>Logout</button>
                </form>
            </nav>
        </aside>
        <main class="col-lg-10 dashboard-main">
            <nav class="dashboard-topbar navbar px-4 py-3">
                <div class="container-fluid">
                    <button class="btn btn-dark d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#technicianSidebar" aria-controls="technicianSidebar" aria-label="Buka menu"><i class="fa-solid fa-bars" aria-hidden="true"></i></button>
                    <span class="fw-bold">Dashboard Teknisi</span>
                    <div class="ms-auto d-flex align-items-center gap-3">
                        <span class="badge bg-light text-dark">{{ Auth::user()->name }}</span>
                        <x-notifications-dropdown />
                    </div>
                </div>
            </nav>
            <div class="dashboard-content">
                @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
                @if (session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
                @yield('content')
                @include('components.dashboard-footer')
            </div>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<div>
    <!-- The best way to take care of the future is to take care of the present moment. - Thich Nhat Hanh -->
</div>
