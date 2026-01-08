@extends('admin.layout')
@section('title','Dashboard')
@section('content')

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
  <div>
    <div class="text-muted">Ringkasan</div>
    <h4 class="mb-0">Data Tempat</h4>
  </div>

  <div class="d-flex flex-wrap gap-2">
    <a class="btn btn-dark" href="/admin/tempat/create"><i class="bi bi-plus-lg me-2"></i>Tambah Tempat</a>
    <a class="btn btn-outline-dark" href="/admin/fasilitas"><i class="bi bi-box-seam me-2"></i>Kelola Fasilitas</a>
    <a class="btn btn-outline-dark" href="/admin/report"><i class="bi bi-bar-chart me-2"></i>Laporan</a>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width:80px">ID</th>
            <th>Nama Tempat</th>
            <th style="width:160px">Harga/Malam</th>
            <th style="width:120px">Status</th>
            <th style="width:120px">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach($list as $t)
            <tr>
              <td>#{{ $t->ID_TEMPAT }}</td>
              <td>{{ $t->NAMA_TEMPAT }}</td>
              <td>Rp{{ number_format($t->HARGA_PER_MALAM) }}</td>
              <td>
                <span class="badge {{ $t->STATUS==='TERSEDIA' ? 'bg-success' : 'bg-secondary' }}">
                  {{ $t->STATUS }}
                </span>
              </td>
              <td>
                <a class="btn btn-sm btn-primary" href="/admin/tempat/{{ $t->ID_TEMPAT }}/edit">Edit</a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection
