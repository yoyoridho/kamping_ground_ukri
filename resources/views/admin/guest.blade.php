<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin - Camping Booking</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    body{ background:#f6f7fb; }
    .card{ border-radius: 16px; }
    .btn,.form-control{ min-height:44px; }
  </style>
</head>
<body>

  <nav class="navbar navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2" href="/admin/login">
        <i class="bi bi-shield-lock"></i>
        <span>Admin Panel</span>
      </a>
      <a class="btn btn-sm btn-outline-light" href="/">Ke Website</a>
    </div>
  </nav>

  <div class="container py-4">
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
