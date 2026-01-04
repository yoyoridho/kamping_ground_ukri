@extends('layout')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0">Detail Booking #{{ $tiket->ID_TIKET }}</h4>
    <div class="text-muted small">Ringkasan booking dan pembayaran</div>
  </div>
  <a class="btn btn-outline-secondary" href="/booking">Kembali</a>
</div>

<div class="row g-3">
  <div class="col-lg-7">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title">Informasi Booking</h5>
        @if($tiket->tempat && $tiket->tempat->FOTO_TEMPAT)
  <img src="{{ asset('storage/'.$tiket->tempat->FOTO_TEMPAT) }}"
       class="img-fluid rounded mb-3"
       style="max-height:260px; object-fit:cover; width:100%;">
@endif
        <hr>
        <div class="mb-2"><b>Tempat:</b> {{ $tiket->tempat->NAMA_TEMPAT ?? '-' }}</div>
        <div class="mb-2"><b>Tanggal:</b> {{ $tiket->TANGGAL_MULAI }} s/d {{ $tiket->TANGGAL_SELESAI }} ({{ $nights }} malam)</div>
        <div class="mb-2"><b>Jumlah Orang:</b> {{ $tiket->JUMLAH_ORANG }}</div>

        <hr>
        <div class="mb-1"><b>Total Tempat:</b> Rp{{ number_format($totalTempat) }}</div>
        <div class="mb-1"><b>Total Addon:</b> Rp{{ number_format($totalAddon) }}</div>
        <div class="fs-5 mt-2"><b>Grand Total:</b> Rp{{ number_format($grandTotal) }}</div>
      </div>
    </div>

    <div class="card shadow-sm mt-3">
      <div class="card-body">
        <h5 class="card-title">Addon</h5>
        <hr>
        @if($tiket->addons->count() === 0)
          <div class="text-muted">Tidak ada addon.</div>
        @else
          <ul class="mb-0">
            @foreach($tiket->addons as $a)
              <li>{{ $a->NAMA_BARANG }} — Rp{{ $a->HARGA_BARANG }} x {{ $a->QTY ?? 1 }}</li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card shadow-sm">
      <div class="card-body"></a>
        <hr>

        @php $st = $tiket->pembayaran->STATUS_BAYAR ?? 'BELUM ADA'; @endphp
        <div class="mb-2">
          Status:
          <span class="badge
            @if($st==='LUNAS') bg-success
            @elseif($st==='PENDING') bg-warning text-dark
            @elseif($st==='GAGAL') bg-danger
            @elseif($st==='EXPIRED') bg-secondary
            @else bg-dark @endif">
            {{ $st }}
          </span>
        </div>

        @if(!$tiket->pembayaran)
          <a class="btn btn-success w-100" href="/booking/{{ $tiket->ID_TIKET }}/pay">Buat Pembayaran</a>
        @else
          <a class="btn btn-primary w-100" href="/payment/{{ $tiket->pembayaran->ID_PEMBAYARAN }}/pay">Buka Halaman Bayar</a>
        @endif

        @if($st==='LUNAS')
          <div class="alert alert-success mt-3 mb-0">
            Pembayaran sukses.
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

@endsection
