@extends('layout')
@section('content')

<div class="d-flex justify-content-between align-items-start align-items-md-center mb-3 gap-2">
  <div>
    <h4 class="mb-0">Booking Saya</h4>
    <div class="text-muted small">Riwayat booking dan status pembayaran</div>
  </div>

  <div class="d-grid d-md-block" style="min-width:160px;">
    @if(!empty($blocking))
      <a class="btn btn-secondary w-100" href="/booking/{{ $blocking->ID_TIKET }}">
        <i class="bi bi-hourglass-split me-1"></i>
        Pending
      </a>
    @else
      <a class="btn btn-success w-100" href="/booking/wizard">
        <i class="bi bi-plus-circle me-1"></i>
        Buat Booking
      </a>
    @endif
  </div>
</div>

{{-- Desktop/table view --}}
<div class="card shadow-sm d-none d-md-block">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width:80px">ID</th>
            <th>Tempat</th>
            <th>Tanggal</th>
            <th style="width:110px">Orang</th>
            <th style="width:140px">Status Bayar</th>
            <th style="width:120px">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($bookings as $b)
            <tr>
              <td>#{{ $b->ID_TIKET }}</td>
              <td>{{ $b->tempat->NAMA_TEMPAT ?? '-' }}</td>
              <td>{{ $b->TANGGAL_MULAI }} s/d {{ $b->TANGGAL_SELESAI }}</td>
              <td>{{ $b->JUMLAH_ORANG }}</td>
              <td>
                @php $st = $b->pembayaran->STATUS_BAYAR ?? 'BELUM BAYAR'; @endphp
                <span class="badge
                  @if($st==='LUNAS') bg-success
                  @elseif($st==='PENDING') bg-warning text-dark
                  @elseif($st==='GAGAL') bg-danger
                  @elseif($st==='EXPIRED') bg-secondary
                  @else bg-dark @endif">
                  {{ $st }}
                </span>
              </td>
              <td>
                <a class="btn btn-sm btn-primary" href="/booking/{{ $b->ID_TIKET }}">Detail</a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted py-4">Belum ada booking.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- Mobile/cards view --}}
<div class="d-md-none">
  @forelse($bookings as $b)
    @php
      $st = $b->pembayaran->STATUS_BAYAR ?? 'BELUM BAYAR';
      $badge = 'bg-dark';
      if($st==='LUNAS') $badge='bg-success';
      elseif($st==='PENDING') $badge='bg-warning text-dark';
      elseif($st==='GAGAL') $badge='bg-danger';
      elseif($st==='EXPIRED') $badge='bg-secondary';
    @endphp

    <a href="/booking/{{ $b->ID_TIKET }}" class="text-decoration-none text-body">
      <div class="card shadow-sm mb-2">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between gap-2">
            <div>
              <div class="small text-muted">Booking</div>
              <div class="fw-semibold">#{{ $b->ID_TIKET }} • {{ $b->tempat->NAMA_TEMPAT ?? '-' }}</div>
              <div class="small text-muted mt-1">
                <i class="bi bi-calendar-event me-1"></i>{{ $b->TANGGAL_MULAI }} — {{ $b->TANGGAL_SELESAI }}
              </div>
              <div class="small text-muted">
                <i class="bi bi-people me-1"></i>{{ $b->JUMLAH_ORANG }} orang
              </div>
            </div>
            <span class="badge {{ $badge }}" style="height:fit-content;">{{ $st }}</span>
          </div>

          <div class="mt-3 d-grid">
            <span class="btn btn-primary">
              Lihat Detail
              <i class="bi bi-chevron-right ms-1"></i>
            </span>
          </div>
        </div>
      </div>
    </a>
  @empty
    <div class="card shadow-sm">
      <div class="card-body text-center text-muted py-4">
        Belum ada booking.
      </div>
    </div>
  @endforelse
</div>

@endsection
