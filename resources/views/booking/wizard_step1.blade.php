@extends('layout')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0">Booking - Step 1</h4>
    <div class="text-muted small">Pilih tempat dan isi detail booking.</div>
  </div>
  <a class="btn btn-outline-secondary" href="/">Kembali</a>
</div>

<form method="POST" action="{{ route('booking.wizard.postStep1') }}" class="card shadow-sm">
  @csrf
  <div class="card-body p-4">

    <div class="mb-3">
      <label class="form-label fw-semibold">Pilih Tempat</label>

      <div class="row g-3">
        @foreach($tempatList as $t)
          @php
            $status = strtoupper((string)($t->STATUS ?? ''));
            $available = ($status === 'TERSEDIA');
            $isSelected = ((int)old('ID_TEMPAT', $selectedTempatId ?? 0) === (int)$t->ID_TEMPAT);
          @endphp

          <div class="col-sm-6 col-lg-4">
            <label class="card h-100 shadow-sm {{ $isSelected ? 'border border-primary' : '' }}"
                   style="cursor:pointer;">
              <div class="p-2 d-flex align-items-center gap-2">
                <input class="form-check-input m-0"
                       type="radio"
                       name="ID_TEMPAT"
                       value="{{ $t->ID_TEMPAT }}"
                       required
                       @checked($isSelected)
                       @disabled(!$available)>
                <div class="fw-semibold">{{ $t->NAMA_TEMPAT }}</div>
                <span class="ms-auto badge {{ $available ? 'bg-success' : 'bg-secondary' }}">
                  {{ $t->STATUS }}
                </span>
              </div>

              @if(!empty($t->FOTO_TEMPAT))
                <img src="{{ asset('storage/'.$t->FOTO_TEMPAT) }}"
                     class="card-img-top"
                     style="height:170px;object-fit:cover;"
                     alt="Foto {{ $t->NAMA_TEMPAT }}">
              @else
                <div class="bg-secondary-subtle d-flex align-items-center justify-content-center"
                     style="height:170px;">
                  <span class="text-muted">Tidak ada foto</span>
                </div>
              @endif

              <div class="card-body">
                <div class="text-muted small">
                  Rp{{ number_format((float)$t->HARGA_PER_MALAM) }} / malam
                </div>
                @if(!$available)
                  <div class="small text-muted mt-2">Tidak tersedia.</div>
                @endif
              </div>
            </label>
          </div>
        @endforeach
      </div>
    </div>

    <hr class="my-4">

    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label fw-semibold">Tanggal Mulai</label>
        <input type="date" name="TANGGAL_MULAI" class="form-control"
               value="{{ old('TANGGAL_MULAI', $old['TANGGAL_MULAI'] ?? '') }}" required>
      </div>
      <div class="col-md-4">
        <label class="form-label fw-semibold">Tanggal Selesai</label>
        <input type="date" name="TANGGAL_SELESAI" class="form-control"
               value="{{ old('TANGGAL_SELESAI', $old['TANGGAL_SELESAI'] ?? '') }}" required>
      </div>
      <div class="col-md-4">
        <label class="form-label fw-semibold">Jumlah Orang</label>
        <input type="number" min="1" name="JUMLAH_ORANG" class="form-control"
               value="{{ old('JUMLAH_ORANG', $old['JUMLAH_ORANG'] ?? 1) }}" required>
      </div>
    </div>

  </div>

  <div class="card-footer d-flex justify-content-between">
    <a class="btn btn-outline-secondary" href="/">Batal</a>
    <button class="btn btn-primary">Next → Pilih Fasilitas</button>
  </div>
</form>

@endsection
