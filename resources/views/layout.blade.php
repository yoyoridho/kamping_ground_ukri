<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Camping Booking</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    :root{ --safe-bottom: env(safe-area-inset-bottom, 0px); }
    body{ padding-bottom: calc(76px + var(--safe-bottom)); }

    .app-topbar{ position: sticky; top:0; z-index: 1020; }

    /* Mobile bottom nav */
    .mobile-bottom-nav{
      position: fixed; left:0; right:0; bottom:0;
      z-index: 1030;
      background: rgba(255,255,255,.96);
      backdrop-filter: blur(10px);
      border-top: 1px solid rgba(0,0,0,.08);
      padding-bottom: var(--safe-bottom);
    }
    .mobile-bottom-nav a{
      text-decoration:none;
      color:#495057;
      font-size:.78rem;
      padding:.45rem .25rem;
      display:flex;
      flex-direction:column;
      align-items:center;
      justify-content:center;
      gap:.15rem;
    }
    .mobile-bottom-nav a .bi{ font-size:1.25rem; }
    .mobile-bottom-nav a.active{ color:#0d6efd; }

    /* Better tap targets */
    .btn, .form-control, .form-select{ min-height: 44px; }

    /* Cards look nicer */
    .card{ border-radius: 14px; }

    /* Hide bottom nav on md+ */
    @media (min-width: 768px){
      body{ padding-bottom: 0; }
      .mobile-bottom-nav{ display:none !important; }
    }

    /* Compact tables on mobile (if still used) */
    @media (max-width: 767.98px){
      .table{ font-size: .9rem; }
    }

    /* Nice skeleton shimmer (optional) */
    .shimmer{ position:relative; overflow:hidden; }
    .shimmer:after{
      content:"";
      position:absolute; top:0; left:-150px; height:100%; width:150px;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,.55), transparent);
      animation: shimmer 1.2s infinite;
    }
    @keyframes shimmer{ 0%{ left:-150px; } 100%{ left:100%; } }
  </style>
</head>
<body class="bg-light">

<div class="app-topbar">
  <nav class="navbar navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand fw-semibold d-flex align-items-center gap-2" href="/booking">
        <i class="bi bi-tent"></i>
        <span>Camping</span>
      </a>

      <div class="d-none d-lg-flex align-items-center gap-2">
        <a class="btn btn-sm btn-outline-light" href="/">Dashboard</a>
        <a class="btn btn-sm btn-outline-light" href="{{ auth('pengunjung')->check() ? '/booking/wizard' : '/login' }}">Booking</a>
        <a class="btn btn-sm btn-outline-light" href="{{ auth('pengunjung')->check() ? '/booking' : '/login' }}">Booking Saya</a>
        <a class="btn btn-sm btn-outline-info" href="/admin/login">Admin</a>
      </div>

      <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#appMenu" aria-controls="appMenu">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="appMenu" aria-labelledby="appMenuLabel">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title" id="appMenuLabel">Menu</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
          <div class="mb-3">
            @auth('pengunjung')
              <div class="small text-white-50">Login sebagai</div>
              <div class="fw-semibold">{{ auth('pengunjung')->user()->NAMA_PENGUNJUNG ?? 'Pengunjung' }}</div>
            @endauth
            @guest('pengunjung')
              <div class="small text-white-50">Belum login</div>
            @endguest
          </div>

          <div class="list-group list-group-flush">
            <a class="list-group-item list-group-item-action bg-dark text-white" href="/">
              <i class="bi bi-grid me-2"></i> Dashboard
            </a>
            <a class="list-group-item list-group-item-action bg-dark text-white" href="{{ auth('pengunjung')->check() ? '/booking/wizard' : '/login' }}">
              <i class="bi bi-plus-circle me-2"></i> Buat Booking
            </a>
            <a class="list-group-item list-group-item-action bg-dark text-white" href="{{ auth('pengunjung')->check() ? '/booking' : '/login' }}">
              <i class="bi bi-receipt me-2"></i> Booking Saya
            </a>
            <a class="list-group-item list-group-item-action bg-dark text-white" href="/admin/login">
              <i class="bi bi-shield-lock me-2"></i> Admin
            </a>
          </div>

          <hr class="border-secondary">

          @auth('pengunjung')
            <form method="POST" action="/logout" class="m-0">
              @csrf
              <button class="btn btn-warning w-100">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
              </button>
            </form>
          @endauth

          @guest('pengunjung')
            <div class="d-grid gap-2">
              <a class="btn btn-outline-light" href="/login"><i class="bi bi-box-arrow-in-right me-2"></i> Login</a>
              <a class="btn btn-light" href="/register"><i class="bi bi-person-plus me-2"></i> Register</a>
            </div>
          @endguest
        </div>
      </div>

    </div>
  </nav>
</div>

<div class="container-lg py-3 py-md-4">

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

<!-- Mobile bottom navigation -->
<nav class="mobile-bottom-nav d-md-none">
  <div class="container">
    @php
      $path = request()->path();
      $is = fn($p) => $path === trim($p,'/');
      $isBooking = str_starts_with($path, 'booking');
    @endphp
    <div class="row text-center g-0">
      <div class="col">
        <a href="/" class="{{ $is('') ? 'active' : '' }}">
          <i class="bi bi-grid"></i>
          <span>Home</span>
        </a>
      </div>
      <div class="col">
        <a href="{{ auth('pengunjung')->check() ? '/booking/wizard' : '/login' }}" class="{{ ($path==='booking/wizard' || $path==='booking/wizard/step1' || $path==='booking/wizard/step2') ? 'active' : '' }}">
          <i class="bi bi-plus-circle"></i>
          <span>Booking</span>
        </a>
      </div>
      <div class="col">
        <a href="{{ auth('pengunjung')->check() ? '/booking' : '/login' }}" class="{{ $isBooking && !$path==='booking/wizard' ? 'active' : '' }}">
          <i class="bi bi-receipt"></i>
          <span>Saya</span>
        </a>
      </div>
      <div class="col">
        <a href="/admin/login" class="{{ $is('admin/login') ? 'active' : '' }}">
          <i class="bi bi-shield-lock"></i>
          <span>Admin</span>
        </a>
      </div>
    </div>
  </div>
</nav>

<!-- Confirm Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Konfirmasi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="confirmModalBody">Yakin?</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-danger" id="confirmModalOk">Ya, lanjut</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Auto-hide alerts
  setTimeout(() => {
    document.querySelectorAll('.alert').forEach(a => {
      a.classList.add('fade');
      a.classList.add('show');
      setTimeout(()=> a.remove(), 350);
    });
  }, 3200);

  // Confirm modal for any form/button with data-confirm
  (function(){
    const modalEl = document.getElementById('confirmModal');
    if(!modalEl) return;
    const modal = new bootstrap.Modal(modalEl);
    const body = document.getElementById('confirmModalBody');
    const okBtn = document.getElementById('confirmModalOk');
    let pendingSubmit = null;

    document.addEventListener('submit', function(e){
      const form = e.target;
      const msg = form.getAttribute('data-confirm');
      if(!msg) return;
      e.preventDefault();
      pendingSubmit = () => form.submit();
      body.textContent = msg;
      modal.show();
    }, true);

    okBtn.addEventListener('click', () => {
      modal.hide();
      if(pendingSubmit){ pendingSubmit(); pendingSubmit = null; }
    });
  })();

  // Qty stepper buttons (optional)
  document.addEventListener('click', function(e){
    const btn = e.target.closest('[data-stepper]');
    if(!btn) return;
    const target = document.querySelector(btn.getAttribute('data-target'));
    if(!target) return;
    const delta = parseInt(btn.getAttribute('data-stepper') || '0', 10);
    const min = parseInt(target.getAttribute('min') || '0', 10);
    const maxAttr = target.getAttribute('max');
    const max = maxAttr ? parseInt(maxAttr, 10) : Infinity;
    const cur = parseInt(target.value || '0', 10);
    let next = cur + delta;
    if(Number.isNaN(next)) next = min;
    next = Math.max(min, Math.min(max, next));
    target.value = next;
    target.dispatchEvent(new Event('input', {bubbles:true}));
  });
</script>

</body>
</html>
