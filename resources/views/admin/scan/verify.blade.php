@extends('admin.layout')
@section('title','Scan Tiket')
@section('content')

<h4 class="mb-3">Verifikasi Tiket (Scan QR)</h4>

@if(session('ok'))
  <div class="alert alert-success">{{ session('ok') }}</div>
@endif
@if(session('err'))
  <div class="alert alert-danger">{{ session('err') }}</div>
@endif

@if(!$tiket)
  <div class="alert alert-danger">
    QR tidak valid / tiket tidak ditemukan. Token: <b>{{ $token }}</b>
  </div>
  @return
@endif

<div class="card shadow-sm mb-3">
  <div class="card-body">
    <div class="fw-semibold">Tiket #{{ $tiket->ID_TIKET }}</div>
    <div class="text-muted small mt-1">
      Tempat: <b>{{ $tiket->tempat->NAMA_TEMPAT ?? '-' }}</b><br>
      Tanggal: <b>{{ $tiket->TANGGAL_MULAI }}</b> s/d <b>{{ $tiket->TANGGAL_SELESAI }}</b><br>
      Orang: <b>{{ $tiket->JUMLAH_ORANG }}</b><br>
      Status bayar: <b>{{ $tiket->STATUS_PEMBAYARAN }}</b><br>
      Check-in: <b>{{ $tiket->CHECKIN_STATUS }}</b>
    </div>
  </div>
</div>

@if(strtoupper((string)$tiket->STATUS_PEMBAYARAN) !== 'TERBAYAR')
  <div class="alert alert-warning">Tiket belum terbayar. Tidak boleh masuk.</div>
@elseif(strtoupper((string)$tiket->CHECKIN_STATUS) === 'SUDAH')
  <div class="alert alert-danger">
    Tiket sudah check-in pada: <b>{{ $tiket->CHECKIN_AT }}</b>
  </div>
@else
  <form method="POST" action="{{ route('admin.scan.checkin', $tiket->QR_TOKEN) }}">
    @csrf
    <button class="btn btn-success btn-lg">✔ Check-in (Tandai Digunakan)</button>
  </form>
@endif

@endsection
