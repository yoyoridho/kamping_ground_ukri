<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Camping Booking</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fw-semibold" href="/booking">Camping</a>

    <div class="ms-auto d-flex gap-2 align-items-center">
      @auth('pengunjung')
        <span class="text-white-50 small me-2">
          {{ auth('pengunjung')->user()->NAMA_PENGUNJUNG ?? 'Pengunjung' }}
        </span>
        <form method="POST" action="/logout" class="m-0">@csrf
          <button class="btn btn-sm btn-warning">Logout</button>
        </form>
      @endauth

      @guest('pengunjung')
        <a class="btn btn-sm btn-outline-light" href="/login">Login</a>
        <a class="btn btn-sm btn-light" href="/register">Register</a>
      @endguest

      <a class="btn btn-sm btn-outline-info" href="/admin/login">Admin</a>
    </div>
  </div>
</nav>

<div class="container py-4">
  @if(session('ok'))
    <div class="alert alert-success">{{ session('ok') }}</div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  @yield('content')
</div>

</body>
</html>
