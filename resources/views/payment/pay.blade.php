@extends('layout')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0">Payment #{{ $pay->ID_PEMBAYARAN }}</h4>  </div>
  <a class="btn btn-outline-secondary" href="/booking/{{ $pay->ID_TIKET }}">Kembali</a>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-6">
        <div><b>Booking:</b> #{{ $pay->ID_TIKET }}</div>
        <div><b>Tempat:</b> {{ $pay->tiket->tempat->NAMA_TEMPAT ?? '-' }}</div>
        <div><b>Tanggal:</b> {{ $pay->tiket->TANGGAL_MULAI }} s/d {{ $pay->tiket->TANGGAL_SELESAI }}</div>
      </div>
      <div class="col-md-6">
        <div><b>Metode:</b> {{ $pay->METODE_BAYAR }}</div>
        <div><b>Jumlah:</b> Rp{{ number_format($pay->JUMLAH_PEMBAYARAN) }}</div>
        <div><b>Status:</b> {{ $pay->STATUS_BAYAR }}</div>
      </div>
    </div>

    <hr>

    @if($pay->STATUS_BAYAR === 'PENDING')
      <form method="POST" action="/payment/{{ $pay->ID_PEMBAYARAN }}/simulate" class="d-flex flex-wrap gap-2">
        @csrf
        <button name="result" value="success" class="btn btn-success">Simulasikan Sukses</button>
        <button name="result" value="failed" class="btn btn-danger">Simulasikan Gagal</button>
        <button name="result" value="expired" class="btn btn-secondary">Simulasikan Expired</button>
      </form>
      <div class="text-muted small mt-2">
        ....
      </div>
    @else
      <div class="alert alert-info mb-0">
        Payment sudah final: <b>{{ $pay->STATUS_BAYAR }}</b>
      </div>
    @endif
  </div>
</div>

@endsection
