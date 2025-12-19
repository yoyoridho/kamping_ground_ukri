@extends('layout')
@section('content')

<div class="row justify-content-center">
  <div class="col-md-6 col-lg-5">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h4 class="mb-3">Login Admin (Pegawai)</h4>

        <form method="POST" action="/admin/login">
          @csrf

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input name="EMAIL_PEGAWAI" type="email" class="form-control" value="{{ old('EMAIL_PEGAWAI') }}" required>
          </div>

          <div class="mb-4">
            <label class="form-label">Password</label>
            <input name="PASSWORD_PEGAWAI" type="password" class="form-control" required>
          </div>

          <button class="btn btn-dark w-100">Login Admin</button>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection
