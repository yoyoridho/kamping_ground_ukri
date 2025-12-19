@extends('layout')
@section('content')

<h4 class="mb-3">Tambah Tempat</h4>

<form method="POST" action="/admin/tempat" class="card shadow-sm" enctype="multipart/form-data">

  @csrf
  <div class="card-body p-4">
    <div class="mb-3">
      <label class="form-label">Nama Tempat</label>
      <input name="NAMA_TEMPAT" class="form-control" value="{{ old('NAMA_TEMPAT') }}" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Harga Per Malam</label>
      <input name="HARGA_PER_MALAM" class="form-control" value="{{ old('HARGA_PER_MALAM') }}" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Status</label>
      <select name="STATUS" class="form-select">
        <option value="TERSEDIA">TERSEDIA</option>
        <option value="BOOKED">BOOKED</option>
      </select>
    </div>
  </div>

<div class="mb-3">
  <label class="form-label">Tambahkan Foto </label>
  <input type="file" name="FOTO_TEMPAT" class="form-control" accept="image/*">
  <div class="form-text">jpg/png/webp, max 2MB</div>
</div>

  <div class="card-footer d-flex justify-content-between">
    <a class="btn btn-outline-secondary" href="/admin/tempat">Kembali</a>
    <button class="btn btn-primary">Simpan</button>
  </div>
</form>

@endsection
