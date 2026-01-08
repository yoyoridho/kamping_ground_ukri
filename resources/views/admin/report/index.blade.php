@extends('admin.layout')
@section('title','Laporan')
@section('content')

<h4 class="mb-3">Laporan Pembayaran</h4>

<form class="row g-2 mb-3">
  <div class="col-md-3">
    <input type="date" name="from" class="form-control" value="{{ request('from') }}">
  </div>
  <div class="col-md-3">
    <input type="date" name="to" class="form-control" value="{{ request('to') }}">
  </div>
  <div class="col-md-3">
    <select name="status" class="form-select">
      <option value="">-- semua status --</option>
      @foreach(['PENDING','LUNAS','GAGAL','EXPIRED'] as $s)
        <option value="{{ $s }}" @selected(request('status')===$s)>{{ $s }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-3">
    <button class="btn btn-primary w-100">Filter</button>
  </div>
</form>

<div class="card shadow-sm">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width:80px">ID</th>
            <th style="width:90px">Tiket</th>
            <th>Pengunjung</th>
            <th>Tempat</th>
            <th style="width:140px">Tanggal Bayar</th>
            <th style="width:140px">Jumlah</th>
            <th style="width:120px">Status</th>
          </tr>
        </thead>
        <tbody>
          @foreach($rows as $r)
            <tr>
              <td>#{{ $r->ID_PEMBAYARAN }}</td>
              <td>#{{ $r->ID_TIKET }}</td>
              <td>{{ $r->tiket->pengunjung->NAMA_PENGUNJUNG ?? '-' }}</td>
              <td>{{ $r->tiket->tempat->NAMA_TEMPAT ?? '-' }}</td>
              <td>{{ $r->TANGGAL_PEMBAYARAN ?? '-' }}</td>
              <td>Rp{{ number_format($r->JUMLAH_PEMBAYARAN) }}</td>
              <td>{{ $r->STATUS_BAYAR }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

<a class="btn btn-outline-secondary mt-3" href="/admin/tempat">Kembali</a>

@endsection
