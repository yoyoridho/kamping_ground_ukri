@extends('admin.layout')
@section('title','Fasilitas')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0">Kelola Fasilitas</h4>
    <div class="text-muted small">Atur stok, harga, dan status fasilitas.</div>
  </div>
  <a class="btn btn-primary" href="/admin/fasilitas/create">Tambah Fasilitas</a>
</div>

<form class="row g-2 mb-3" method="GET" action="/admin/fasilitas">
  <div class="col-md-8">
    <input class="form-control" name="q" value="{{ request('q') }}" placeholder="Cari fasilitas...">
  </div>
  <div class="col-md-4 d-flex gap-2">
    <button class="btn btn-dark w-100" type="submit">Cari</button>
    <a class="btn btn-outline-secondary w-100" href="/admin/fasilitas">Reset</a>
  </div>
</form>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-bordered align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Nama</th>
          <th style="width:160px;">Harga</th>
          <th style="width:240px;">Stok</th>
          <th style="width:150px;">Status</th>
          <th style="width:160px;">Kondisi</th>
          <th style="width:200px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($list as $f)
          @php
            $stok = (int)($f->STOK ?? 0);
            $aktif = strtoupper((string)$f->STATUS) === 'AKTIF';
            $habis = $stok <= 0;
            $menipis = $stok > 0 && $stok <= 3;
          @endphp

          <tr class="{{ $habis ? 'table-warning' : ($menipis ? 'table-light' : '') }}">
            <td>
              <div class="fw-semibold">{{ $f->NAMA_FASILITAS }}</div>
              @if(!empty($f->DESKRIPSI))
                <div class="text-muted small">{{ $f->DESKRIPSI }}</div>
              @endif
            </td>

            <td>Rp{{ number_format((int)$f->HARGA_FASILITAS) }}</td>

            <td>
              <form method="POST" action="/admin/fasilitas/{{ $f->ID_FASILITAS }}/stok" class="d-flex gap-2">
                @csrf
                <input type="number" min="0" name="STOK" value="{{ $stok }}" class="form-control">
                <button class="btn btn-outline-primary" type="submit">Simpan</button>
              </form>
            </td>

            <td>
              <span class="badge {{ $aktif ? 'text-bg-success' : 'text-bg-secondary' }}">
                {{ $f->STATUS }}
              </span>
            </td>

            <td>
              @if(!$aktif)
                <span class="badge text-bg-secondary">nonaktif</span>
              @elseif($habis)
                <span class="badge text-bg-danger">stok habis</span>
              @elseif($menipis)
                <span class="badge text-bg-warning">stok menipis</span>
              @else
                <span class="badge text-bg-success">ready</span>
              @endif
            </td>

            <td>
              <div class="d-flex gap-2">
                <a class="btn btn-outline-secondary btn-sm" href="/admin/fasilitas/{{ $f->ID_FASILITAS }}/edit">Edit</a>
                <form method="POST" action="/admin/fasilitas/{{ $f->ID_FASILITAS }}/delete" onsubmit="return confirm('Hapus fasilitas ini?');">
                  @csrf
                  <button class="btn btn-outline-danger btn-sm" type="submit">Hapus</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted py-3">Belum ada data fasilitas.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection
