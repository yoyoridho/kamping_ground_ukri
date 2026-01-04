@extends('layout')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0">Master Fasilitas</h4>
    <div class="text-muted small">Kelola fasilitas sewa</div>
  </div>

  <div class="d-flex gap-2">
    <a class="btn btn-success" href="/admin/fasilitas/create">+ Tambah Fasilitas</a>
    <a class="btn btn-outline-secondary" href="/admin/tempat">Master Tempat</a>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width:90px">ID</th>
            <th>Nama</th>
            <th style="width:180px">Harga</th>
            <th style="width:120px">Status</th>
            <th style="width:160px">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($list as $f)
            <tr>
              <td>#{{ $f->ID_FASILITAS }}</td>
              <td>
                <div class="fw-semibold">{{ $f->NAMA_FASILITAS }}</div>
                @if($f->DESKRIPSI)
                  <div class="text-muted small">{{ $f->DESKRIPSI }}</div>
                @endif
              </td>
              <td>Rp{{ number_format((int)$f->HARGA_FASILITAS) }}</td>
              <td>
                <span class="badge {{ $f->STATUS === 'AKTIF' ? 'bg-success' : 'bg-secondary' }}">
                  {{ $f->STATUS }}
                </span>
              </td>
              <td class="d-flex gap-2">
                <a class="btn btn-sm btn-primary" href="/admin/fasilitas/{{ $f->ID_FASILITAS }}/edit">Edit</a>
                <form method="POST" action="/admin/fasilitas/{{ $f->ID_FASILITAS }}/delete" class="m-0"
                      onsubmit="return confirm('Hapus fasilitas ini?');">
                  @csrf
                  <button class="btn btn-sm btn-danger">Hapus</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center text-muted py-4">Belum ada fasilitas.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection
