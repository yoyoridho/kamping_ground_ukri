@extends('layout')
@section('content')

<h4 class="mb-3">Tambah Fasilitas</h4>

<form method="POST" action="/admin/fasilitas" class="card shadow-sm">
  @csrf
  <div class="card-body p-4">

    <div class="mb-3">
      <label class="form-label">Nama Fasilitas</label>
      <input name="NAMA_FASILITAS" class="form-control" value="{{ old('NAMA_FASILITAS') }}" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Harga (per malam)</label>
      <input name="HARGA_FASILITAS" type="number" min="0" class="form-control" value="{{ old('HARGA_FASILITAS', 0) }}" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Status</label>
      <select name="STATUS" class="form-select">
        <option value="AKTIF" @selected(old('STATUS')==='AKTIF')>AKTIF</option>
        <option value="NONAKTIF" @selected(old('STATUS')==='NONAKTIF')>NONAKTIF</option>
      </select>
    </div>

    <div class="mb-0">
      <label class="form-label">Deskripsi (opsional)</label>
      <input name="DESKRIPSI" class="form-control" value="{{ old('DESKRIPSI') }}">
    </div>

  </div>

  <div class="card-footer d-flex justify-content-between">
    <a class="btn btn-outline-secondary" href="/admin/fasilitas">Kembali</a>
    <button class="btn btn-primary">Simpan</button>
  </div>
</form>

@endsection
