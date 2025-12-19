@extends('layout')
@section('content')

<div class="row justify-content-center">
  <div class="col-md-6 col-lg-5">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h4 class="mb-3">Register Pengunjung</h4>

        <form method="POST" action="/register">
          @csrf

          <div class="mb-3">
            <label class="form-label">Nama</label>
            <input name="NAMA_PENGUNJUNG" class="form-control" value="{{ old('NAMA_PENGUNJUNG') }}" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input name="GMAIL" type="email" class="form-control" value="{{ old('GMAIL') }}" required>
          </div>

          <div class="mb-3">
            <label class="form-label">No HP</label>
            <input name="NO_HP_PENGUNJUNG" class="form-control" value="{{ old('NO_HP_PENGUNJUNG') }}">
          </div>

          <div class="mb-3">
            <label class="form-label">Password</label>
            <input name="PASSWORD" type="password" class="form-control" required>
          </div>

          <div class="mb-4">
            <label class="form-label">Konfirmasi Password</label>
            <input name="PASSWORD_confirmation" type="password" class="form-control" required>
          </div>

          <button class="btn btn-primary w-100">Daftar</button>

          <div class="text-center mt-3">
            <a href="/login" class="small">Sudah punya akun? Login</a>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

@endsection
