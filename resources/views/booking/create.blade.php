@extends('layout')
@section('content')

<h4 class="mb-3">Buat Booking</h4>

<form method="POST" action="/booking" class="card shadow-sm">
  @csrf
  <div class="card-body p-4">

   <div class="mb-2">
  <label class="form-label">Pilih Tempat</label>
  <div class="row g-3">
    @foreach($tempatList as $t)
      <div class="col-md-4">
        <label class="card h-100 shadow-sm" style="cursor:pointer;">
          <div class="p-2">
            <input class="form-check-input" type="radio" name="ID_TEMPAT" value="{{ $t->ID_TEMPAT }}" required>
            <span class="ms-2 fw-semibold">{{ $t->NAMA_TEMPAT }}</span>
          </div>

          @if($t->FOTO_TEMPAT)
            <img src="{{ asset('storage/'.$t->FOTO_TEMPAT) }}" class="card-img-top" style="height:170px; object-fit:cover;">
          @else
            <div class="bg-secondary-subtle d-flex align-items-center justify-content-center" style="height:170px;">
              <span class="text-muted">Tidak ada foto</span>
            </div>
          @endif

          <div class="card-body">
            <div class="small text-muted">Rp{{ number_format($t->HARGA_PER_MALAM) }} / malam</div>
            <span class="badge {{ $t->STATUS==='TERSEDIA' ? 'bg-success' : 'bg-secondary' }}">
              {{ $t->STATUS }}
            </span>
          </div>
        </label>
      </div>
    @endforeach
  </div>
</div>


    <div class="row">
      <div class="col-md-4 mb-3">
        <label class="form-label">Tanggal Mulai</label>
        <input type="date" name="TANGGAL_MULAI" class="form-control" value="{{ old('TANGGAL_MULAI') }}" required>
      </div>
      <div class="col-md-4 mb-3">
        <label class="form-label">Tanggal Selesai</label>
        <input type="date" name="TANGGAL_SELESAI" class="form-control" value="{{ old('TANGGAL_SELESAI') }}" required>
      </div>
      <div class="col-md-4 mb-3">
        <label class="form-label">Jumlah Orang</label>
        <input type="number" min="1" name="JUMLAH_ORANG" class="form-control" value="{{ old('JUMLAH_ORANG', 1) }}" required>
      </div>
    </div>

    <hr class="my-4">

    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h5 class="mb-0">Addon Sewa (Opsional)</h5>
        <div class="text-muted small">Isi qty jika ingin menyewa. Qty 0 = diabaikan.</div>
      </div>
    </div>

    <div class="mt-3">
      @foreach($rentalItems as $i => $item)
        <div class="row g-2 align-items-center mb-2">
          <div class="col-md-6">
            <input class="form-control" value="{{ $item['name'] }}" readonly>
            <input type="hidden" name="addons[{{ $i }}][name]" value="{{ $item['name'] }}">
            <input type="hidden" name="addons[{{ $i }}][price]" value="{{ $item['price'] }}">
          </div>
          <div class="col-md-3">
            <input class="form-control" value="Rp{{ number_format($item['price']) }}" readonly>
          </div>
          <div class="col-md-3">
            <input type="number" min="0" name="addons[{{ $i }}][qty]" class="form-control" value="0">
          </div>
        </div>
      @endforeach
    </div>

  </div>

  <div class="card-footer d-flex justify-content-between">
    <a class="btn btn-outline-secondary" href="/booking">Kembali</a>
    <button class="btn btn-primary">Simpan Booking</button>
  </div>
</form>

<script>
document.querySelector('form').addEventListener('submit', function(){
  const qtyInputs = [...document.querySelectorAll('input[name^="addons"][name$="[qty]"]')];
  qtyInputs.forEach(inp => {
    const val = parseInt(inp.value || '0', 10);
    if (val <= 0) {
      const idx = inp.name.match(/addons\[(\d+)\]/)[1];
      document.querySelectorAll(`[name^="addons[${idx}]"]`).forEach(el => el.disabled = true);
    }
  });
});
</script>

@endsection
