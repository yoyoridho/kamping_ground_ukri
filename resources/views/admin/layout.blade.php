<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin - Camping Booking</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    :root{ --sidebar-w: 264px; }
    body{ background:#f6f7fb; }
    .card{ border-radius: 16px; }
    .btn,.form-control,.form-select{ min-height:44px; }
    .admin-shell{ min-height: 100vh; }
    .admin-sidebar{
      width: var(--sidebar-w);
      background: #0f172a;
      color: rgba(255,255,255,.9);
    }
    .admin-sidebar .nav-link{ color: rgba(255,255,255,.78); border-radius: 10px; }
    .admin-sidebar .nav-link:hover{ background: rgba(255,255,255,.08); color: #fff; }
    .admin-sidebar .nav-link.active{ background: rgba(59,130,246,.22); color:#fff; }
    .admin-topbar{
      position: sticky; top:0; z-index: 1020;
      background: rgba(246,247,251,.92);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(0,0,0,.06);
    }
    .page-title{ font-size: 1.1rem; }
    @media (max-width: 991.98px){
      .admin-sidebar{ display:none; }
    }
  </style>
</head>
<body>

@php
  $path = request()->path();
  $is = fn($p) => $path === trim($p,'/');
  $isPrefix = fn($p) => str_starts_with($path, trim($p,'/'));
@endphp

<div class="admin-shell d-flex">
  <!-- Sidebar (desktop) -->
  <aside class="admin-sidebar p-3 d-none d-lg-flex flex-column">
    <a class="text-white text-decoration-none d-flex align-items-center gap-2 mb-3" href="/admin">
      <i class="bi bi-shield-lock"></i>
      <span>Admin Panel</span>
    </a>

    <div class="small opacity-75 mb-3">
      {{ auth('pegawai')->user()->EMAIL_PEGAWAI ?? 'Pegawai' }}
    </div>

    <nav class="nav nav-pills flex-column gap-1">
      <a class="nav-link {{ ($is('admin') || $is('admin/tempat')) ? 'active' : '' }}" href="/admin">
        <i class="bi bi-grid me-2"></i> Dashboard
      </a>
      <a class="nav-link {{ $isPrefix('admin/tempat') ? 'active' : '' }}" href="/admin/tempat">
        <i class="bi bi-geo-alt me-2"></i> Tempat
      </a>
      <a class="nav-link {{ $isPrefix('admin/fasilitas') ? 'active' : '' }}" href="/admin/fasilitas">
        <i class="bi bi-box-seam me-2"></i> Fasilitas
      </a>
      <a class="nav-link {{ $isPrefix('admin/report') ? 'active' : '' }}" href="/admin/report">
        <i class="bi bi-bar-chart me-2"></i> Laporan
      </a>
    </nav>

    <div class="mt-auto pt-3 border-top border-light border-opacity-10">
      <div class="d-grid gap-2">
        <a class="btn btn-outline-light" href="/">
          <i class="bi bi-globe2 me-2"></i> Ke Website
        </a>
        <form method="POST" action="/admin/logout" class="m-0">
          @csrf
          <button class="btn btn-warning w-100">
            <i class="bi bi-box-arrow-right me-2"></i> Logout
          </button>
        </form>
      </div>
    </div>
  </aside>

  <!-- Main -->
  <main class="flex-grow-1">
    <!-- Topbar (mobile + desktop) -->
    <div class="admin-topbar">
      <div class="container-fluid py-2 px-3 d-flex align-items-center gap-2">
        <!-- Mobile menu button -->
        <button class="btn btn-dark d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminMenu" aria-controls="adminMenu">
          <i class="bi bi-list"></i>
        </button>

        <div class="page-title">@yield('title', 'Dashboard')</div>

        <div class="ms-auto d-flex align-items-center gap-2">
          <span class="d-none d-md-inline small text-muted">{{ auth('pegawai')->user()->EMAIL_PEGAWAI ?? 'Pegawai' }}</span>
          <form method="POST" action="/admin/logout" class="m-0 d-none d-md-block">
            @csrf
            <button class="btn btn-warning">Logout</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Mobile offcanvas -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="adminMenu" aria-labelledby="adminMenuLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="adminMenuLabel">Admin Panel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <div class="small text-muted mb-3">{{ auth('pegawai')->user()->EMAIL_PEGAWAI ?? 'Pegawai' }}</div>

        <div class="list-group">
          <a class="list-group-item list-group-item-action" href="/admin">Dashboard</a>
          <a class="list-group-item list-group-item-action" href="/admin/tempat">Tempat</a>
          <a class="list-group-item list-group-item-action" href="/admin/fasilitas">Fasilitas</a>
          <a class="list-group-item list-group-item-action" href="/admin/report">Laporan</a>
        </div>

        <hr>
        <div class="d-grid gap-2">
          <a class="btn btn-outline-dark" href="/">Ke Website</a>
          <form method="POST" action="/admin/logout" class="m-0">
            @csrf
            <button class="btn btn-warning w-100">Logout</button>
          </form>
        </div>
      </div>
    </div>

    <div class="container-fluid p-3 p-md-4">
      @if(session('ok'))
        <div class="alert alert-success d-flex align-items-center gap-2" role="alert">
          <i class="bi bi-check-circle"></i>
          <div>{{ session('ok') }}</div>
        </div>
      @endif

      @if($errors->any())
        <div class="alert alert-danger" role="alert">
          <div class="d-flex align-items-center gap-2 mb-2">
            <i class="bi bi-exclamation-triangle"></i>
            <div>Periksa lagi input kamu:</div>
          </div>
          <ul class="mb-0">
            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
          </ul>
        </div>
      @endif

      @yield('content')
    </div>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
