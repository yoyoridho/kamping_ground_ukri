@extends('layout')
@section('content')

<div class="row justify-content-center">
  <div class="col-md-6 col-lg-5">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h4 class="mb-3">Login Pengunjung</h4>

        <form method="POST" action="/login">
          @csrf

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input name="GMAIL" type="email" class="form-control" value="{{ old('GMAIL') }}" required>
          </div>

          <div class="mb-4">
            <label class="form-label">Password</label>
            <input name="PASSWORD" type="password" class="form-control" required>
          </div>

          <button class="btn btn-dark w-100">Login</button>

          <div class="text-center mt-3">
            <a href="/register" class="small">Belum punya akun? Register</a>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

@endsection
