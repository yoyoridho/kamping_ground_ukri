@extends('layout')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0">Booking - Step 2</h4>
    <div class="text-muted small">
      Fasilitas opsional. Boleh qty 0 jika tidak ingin menyewa.
    </div>
  </div>
  <a class="btn btn-outline-secondary" href="{{ route('booking.wizard.step1') }}">← Kembali</a>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-body">
    <div class="row g-3 align-items-center">
      <div class="col-md-8">
        <div class="fw-semibold">Ringkasan Booking</div>
        <div class="text-muted small">
          Tempat: <b>{{ $tempat->NAMA_TEMPAT ?? '-' }}</b>,
          Tanggal: <b>{{ $wizard['TANGGAL_MULAI'] }}</b> s/d <b>{{ $wizard['TANGGAL_SELESAI'] }}</b>,
          Orang: <b>{{ $wizard['JUMLAH_ORANG'] }}</b>
        </div>
      </div>
      <div class="col-md-4 text-md-end">
        <span class="badge {{ strtoupper((string)($tempat->STATUS ?? ''))==='TERSEDIA' ? 'bg-success' : 'bg-secondary' }}">
          {{ $tempat->STATUS ?? '-' }}
        </span>
      </div>
    </div>
  </div>
</div>

<form method="POST" action="{{ route('booking.wizard.finish') }}" class="card shadow-sm">
  @csrf
  <div class="card-body p-4">

    <h5 class="mb-1">Fasilitas Sewa (Opsional)</h5>
    <div class="text-muted small mb-3">
      isi 0 jika tidak ingin menyewa
    </div>

    <div class="table-responsive">
      <table class="table table-bordered align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Nama Fasilitas</th>
            <th style="width:220px;">Harga (per malam)</th>
            <th style="width:160px;">Qty</th>
          </tr>
        </thead>
        <tbody>
          @foreach($rentalItems as $i => $item)
            <tr>
              <td>
                <div class="fw-semibold">{{ $item['name'] }}</div>
                <input type="hidden" name="addons[{{ $i }}][name]" value="{{ $item['name'] }}">
                <input type="hidden" name="addons[{{ $i }}][price]" value="{{ $item['price'] }}">
              </td>
              <td>Rp{{ number_format((float)$item['price']) }}</td>
              <td>
                <input type="number"
                       min="0"
                       name="addons[{{ $i }}][qty]"
                       class="form-control"
                       value="{{ old("addons.$i.qty", 0) }}">
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="text-muted small mt-2">
      *Addon akan dihitung pada total sesuai jumlah malam booking.
    </div>

  </div>

  <div class="card-footer d-flex justify-content-between">
    <a class="btn btn-outline-secondary" href="{{ route('booking.wizard.step1') }}">← Kembali</a>
    <button class="btn btn-success">Simpan Booking</button>
  </div>
</form>

@endsection
