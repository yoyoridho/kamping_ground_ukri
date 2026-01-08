@extends('layout')
@section('content')

@php
  $start = $wizard['TANGGAL_MULAI'];
  $end = $wizard['TANGGAL_SELESAI'];
@endphp

<div class="d-flex justify-content-between align-items-start align-items-md-center mb-3">
  <div>
    <h4 class="mb-0">Booking - Step 2</h4>
    <div class="text-muted small">
      Pilih fasilitas opsional. Isi 0 kalau tidak ingin menyewa.
    </div>
  </div>
  <a class="btn btn-outline-secondary" href="{{ route('booking.wizard.step1') }}">
    <i class="bi bi-arrow-left"></i>
    Kembali
  </a>
</div>

<div class="card shadow-sm mb-3" id="wizardMeta"
     data-start="{{ $start }}"
     data-end="{{ $end }}"
     data-base-price="{{ (float)($tempat->HARGA_PER_MALAM ?? 0) }}">
  <div class="card-body">
    <div class="row g-2 align-items-center">
      <div class="col-12 col-md-8">
        <div class="fw-semibold">Ringkasan Booking</div>
        <div class="text-muted small">
          Tempat: <span class="fw-semibold">{{ $tempat->NAMA_TEMPAT ?? '-' }}</span><br class="d-md-none">
          Tanggal: <span class="fw-semibold">{{ $start }}</span> s/d <span class="fw-semibold">{{ $end }}</span>
          <span class="d-none d-md-inline">•</span>
          <span class="d-block d-md-inline">Orang: <span class="fw-semibold">{{ $wizard['JUMLAH_ORANG'] }}</span></span>
        </div>
      </div>
      <div class="col-12 col-md-4 text-md-end">
        <span class="badge {{ strtoupper((string)($tempat->STATUS ?? ''))==='TERSEDIA' ? 'bg-success' : 'bg-secondary' }}">
          {{ $tempat->STATUS ?? '-' }}
        </span>
        <div class="small text-muted mt-1">
          Rp{{ number_format((float)($tempat->HARGA_PER_MALAM ?? 0)) }} / malam
        </div>
      </div>
    </div>
  </div>
</div>

<form method="POST" action="{{ route('booking.wizard.finish') }}" class="card shadow-sm" id="wizardStep2Form">
  @csrf
  <div class="card-body p-3 p-md-4">

    <div class="d-flex align-items-center justify-content-between mb-2">
      <div>
        <h5 class="mb-0">Fasilitas Sewa (Opsional)</h5>
        <div class="text-muted small">Addon dihitung sesuai jumlah malam</div>
      </div>
      <button type="button" class="btn btn-outline-secondary btn-sm" id="resetQty">
        <i class="bi bi-arrow-counterclockwise"></i>
        Reset
      </button>
    </div>

    {{-- Desktop/table view --}}
    <div class="table-responsive d-none d-md-block" id="desktopView">
      <table class="table table-bordered align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Nama Fasilitas</th>
            <th style="width:220px;">Harga (per malam)</th>
            <th style="width:180px;">Qty</th>
          </tr>
        </thead>
        <tbody>
          @foreach($rentalItems as $item)
            <tr>
              <td>
                <div class="fw-semibold">{{ $item['name'] }}</div>
              </td>
              <td>Rp{{ number_format((float)$item['price']) }}</td>
              <td>
                <div class="input-group">
                  <button class="btn btn-outline-secondary" type="button" data-stepper="-1" data-target="#qty{{ $item['id'] }}">
                    <i class="bi bi-dash"></i>
                  </button>
                  <input id="qty{{ $item['id'] }}" type="number" min="0" max="{{ (int)$item['stok'] }}" name="addons[{{ $item['id'] }}]" class="form-control addon-qty"
                         data-price="{{ (float)$item['price'] }}"
                         data-fid="{{ $item['id'] }}"
                         value="{{ old('addons.'.$item['id'], 0) }}">
                  <button class="btn btn-outline-secondary" type="button" data-stepper="1" data-target="#qty{{ $item['id'] }}">
                    <i class="bi bi-plus"></i>
                  </button>
                </div>
                <div class="small text-muted mt-1">Stok: {{ (int)$item['stok'] }}</div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- Mobile/cards view --}}
    <div class="d-md-none" id="mobileView">
      @foreach($rentalItems as $item)

        <div class="card border-0 shadow-sm mb-2">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start gap-2">
              <div>
                <div class="fw-semibold">{{ $item['name'] }}</div>
                <div class="text-muted small">Rp{{ number_format((float)$item['price']) }} / malam</div>
              </div>
              <div class="text-end">
                <div class="small text-muted">Qty</div>
                <div class="input-group" style="width: 140px;">
                  <button class="btn btn-outline-secondary" type="button" data-stepper="-1" data-target="#mqty{{ $item['id'] }}">
                    <i class="bi bi-dash"></i>
                  </button>
                  <input id="mqty{{ $item['id'] }}" type="number" min="0" max="{{ (int)$item['stok'] }}" name="addons[{{ $item['id'] }}]" class="form-control addon-qty text-center"
                         inputmode="numeric"
                         data-price="{{ (float)$item['price'] }}"
                         data-fid="{{ $item['id'] }}"
                         value="{{ old('addons.'.$item['id'], 0) }}">
                  <button class="btn btn-outline-secondary" type="button" data-stepper="1" data-target="#mqty{{ $item['id'] }}">
                    <i class="bi bi-plus"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="small text-muted mt-2" id="line-{{ $item['id'] }}"></div>
            <div class="small text-muted">Stok: {{ (int)$item['stok'] }}</div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="alert alert-info mt-3 mb-0" id="priceBox">
      <div class="d-flex justify-content-between">
        <span>Perkiraan total</span>
        <span class="fw-semibold" id="grandTotal">Rp0</span>
      </div>
      <div class="small text-muted" id="priceNote">Menghitung…</div>
    </div>

  </div>

  <div class="card-footer d-flex justify-content-between gap-2">
    <a class="btn btn-outline-secondary" href="{{ route('booking.wizard.step1') }}">
      <i class="bi bi-arrow-left"></i> Kembali
    </a>
    <button class="btn btn-success" type="submit">
      <i class="bi bi-check2-circle"></i> Simpan Booking
    </button>
  </div>
</form>

<script>
  (function(){
    const meta = document.getElementById('wizardMeta');
    if(!meta) return;

    const startStr = meta.getAttribute('data-start');
    const endStr = meta.getAttribute('data-end');
    const basePrice = parseFloat(meta.getAttribute('data-base-price') || '0');

    // NOTE: Ada 2 set input (desktop & mobile). Kita hanya boleh submit yang terlihat.
    const qtyInputs = Array.from(document.querySelectorAll('.addon-qty'));
    const grandEl = document.getElementById('grandTotal');
    const noteEl = document.getElementById('priceNote');

    function parseDate(s){
      // Expect YYYY-MM-DD
      const [y,m,d] = (s||'').split('-').map(n=>parseInt(n,10));
      if(!y||!m||!d) return null;
      return new Date(y, m-1, d);
    }

    function nights(){
      const s = parseDate(startStr);
      const e = parseDate(endStr);
      if(!s||!e) return 0;
      const diff = (e - s) / (1000*60*60*24);
      return Math.max(1, Math.round(diff));
    }

    function rupiah(n){
      try{ return new Intl.NumberFormat('id-ID').format(Math.round(n)); }
      catch{ return String(Math.round(n)); }
    }

    function activeQtyInputs(){
      return qtyInputs.filter(i => !i.disabled);
    }

    function compute(){
      const n = nights();
      const baseTotal = basePrice * n;
      let addonPerNight = 0;
      activeQtyInputs().forEach((inp) => {
        const price = parseFloat(inp.getAttribute('data-price') || '0');
        const qty = Math.max(0, parseInt(inp.value || '0', 10) || 0);
        addonPerNight += price * qty;
      });

      const addonTotal = addonPerNight * n;
      const grand = baseTotal + addonTotal;

      grandEl.textContent = 'Rp' + rupiah(grand);
      noteEl.textContent = 'Tempat Rp' + rupiah(baseTotal) + ' + Addon Rp' + rupiah(addonTotal) + ' (x ' + n + ' malam)';

      // Update small line labels (mobile)
      activeQtyInputs().forEach((inp) => {
        const fid = inp.getAttribute('data-fid');
        if(!fid) return;
        const line = document.getElementById('line-' + fid);
        if(!line) return;
        const price = parseFloat(inp.getAttribute('data-price') || '0');
        const qty = Math.max(0, parseInt(inp.value || '0', 10) || 0);
        const perNight = price * qty;
        line.textContent = qty > 0 ? ('Subtotal addon: Rp' + rupiah(perNight) + ' / malam') : 'Tidak disewa';
      });
    }

    function setEnabled(container, enabled){
      if(!container) return;
      container.querySelectorAll('input, select, textarea, button').forEach(el => {
        // jangan disable tombol stepper, biar UI tetap enak (tapi input yang di-submit harus disable)
        if(el.classList && el.classList.contains('addon-qty')){
          el.disabled = !enabled;
        }
        if(el.type === 'hidden' && el.name && el.name.startsWith('addons[')){
          el.disabled = !enabled;
        }
      });
    }

    function applyResponsiveLock(){
      const isDesktop = window.matchMedia('(min-width: 768px)').matches;
      const desktop = document.getElementById('desktopView');
      const mobile = document.getElementById('mobileView');
      setEnabled(desktop, isDesktop);
      setEnabled(mobile, !isDesktop);
      compute();
    }

    qtyInputs.forEach(i => i.addEventListener('input', compute));

    // Stepper (+/-)
    document.addEventListener('click', (e) => {
      const btn = e.target.closest('[data-stepper][data-target]');
      if(!btn) return;
      const targetSel = btn.getAttribute('data-target');
      const step = parseInt(btn.getAttribute('data-stepper') || '0', 10) || 0;
      const inp = targetSel ? document.querySelector(targetSel) : null;
      if(!inp || inp.disabled) return;

      let v = parseInt(inp.value || '0', 10);
      if(isNaN(v)) v = 0;
      v = v + step;
      v = Math.max(0, v);
      const max = parseInt(inp.getAttribute('max') || '', 10);
      if(!isNaN(max)) v = Math.min(max, v);

      inp.value = String(v);
      inp.dispatchEvent(new Event('input', { bubbles: true }));
    });
    // Kunci input supaya yang tidak terlihat tidak ikut terkirim (ini yang bikin addon jadi kosong)
    applyResponsiveLock();
    window.addEventListener('resize', applyResponsiveLock);
    compute();

    const resetBtn = document.getElementById('resetQty');
    if(resetBtn){
      resetBtn.addEventListener('click', () => {
        activeQtyInputs().forEach(i => { i.value = 0; });
        compute();
      });
    }
  })();
</script>

@endsection
