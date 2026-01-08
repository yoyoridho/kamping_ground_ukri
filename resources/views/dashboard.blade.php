@extends('layout')
@section('content')

@php
  $isLoggedIn = auth('pengunjung')->check();

  // tab state (tempat|fasilitas)
 $tab = (string) request()->query('tab', 'tempat');
if (!in_array($tab, ['tempat','fasilitas'], true)) { $tab = 'tempat'; }

// KUNCI: kalau lagi submit filter tempat, paksa balik ke tab tempat
if (request()->hasAny(['q','status','sort'])) {
  $tab = 'tempat';
}


  $totalTempat = $tempatList->count();
  $totalTersedia = $tempatList->filter(fn($t) => strtoupper((string)($t->STATUS ?? '')) === 'TERSEDIA')->count();

  $hargaArr = $tempatList->map(fn($t) => (float)($t->HARGA_PER_MALAM ?? 0))->filter(fn($x) => $x > 0)->values();
  $minHarga = $hargaArr->count() ? $hargaArr->min() : 0;
  $maxHarga = $hargaArr->count() ? $hargaArr->max() : 0;

  $q = (string)request('q', '');
  $statusFilter = (string)request('status', 'ALL');
  $sort = (string)request('sort', 'newest'); // newest | cheapest | priciest | name

  // Filter tempat
  $filtered = $tempatList->filter(function($t) use ($q, $statusFilter) {
    $nama = strtolower((string)($t->NAMA_TEMPAT ?? ''));
    $status = strtoupper((string)($t->STATUS ?? ''));

    $matchQ = $q === '' ? true : str_contains($nama, strtolower($q));
    $matchStatus = ($statusFilter === 'ALL') ? true : ($status === $statusFilter);

    return $matchQ && $matchStatus;
  });

  // Sort tempat
  $sorted = $filtered->sort(function($a, $b) use ($sort) {
    $ha = (float)($a->HARGA_PER_MALAM ?? 0);
    $hb = (float)($b->HARGA_PER_MALAM ?? 0);

    if ($sort === 'cheapest') return $ha <=> $hb;
    if ($sort === 'priciest') return $hb <=> $ha;
    if ($sort === 'name') return strcmp((string)$a->NAMA_TEMPAT, (string)$b->NAMA_TEMPAT);

    return (int)$b->ID_TEMPAT <=> (int)$a->ID_TEMPAT;
  })->values();
@endphp

<style>
  .cg-hero {
    background: radial-gradient(1200px 600px at 20% -10%, rgba(59,130,246,.45), transparent 60%),
                radial-gradient(900px 500px at 90% 0%, rgba(16,185,129,.35), transparent 60%),
                linear-gradient(135deg, #0b1220 0%, #0f172a 45%, #111827 100%);
    border: 1px solid rgba(255,255,255,.08);
  }
  .cg-glass {
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.10);
    backdrop-filter: blur(10px);
  }
  .cg-card {
    border: 1px solid rgba(15,23,42,.08);
    transition: transform .18s ease, box-shadow .18s ease;
  }
  .cg-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 .75rem 1.5rem rgba(2,6,23,.12);
  }
  .cg-img {
    height: 200px;
    object-fit: cover;
  }
  .cg-badge-pill {
    border-radius: 999px;
    padding: .4rem .65rem;
  }
  .cg-muted { color: rgba(255,255,255,.72); }
  .cg-section-title { letter-spacing: .2px; }
</style>

{{-- HERO --}}
<div class="card cg-hero text-white shadow-sm mb-4 border-0">
  <div class="card-body p-4 p-md-5">
    <div class="row g-4 align-items-center">
      <div class="col-lg-7">
        <div class="d-inline-flex align-items-center gap-2 cg-glass rounded-pill px-3 py-2 mb-3">
          <span class="badge bg-light text-dark cg-badge-pill">Info</span>
          <span class="small cg-muted">Swipe/slide untuk lihat Tempat & Fasilitas</span>
        </div>

        <h2 class="fw-bold mb-2">Camping Ground Booking</h2>
        <div class="cg-muted">Lihat tempat, cek fasilitas, lalu booking lewat wizard 2 step.</div>

        <div class="d-flex flex-wrap gap-2 mt-4">
          <a href="/booking/wizard" class="btn btn-primary btn-lg">Booking Sekarang</a>
          <a href="/booking" class="btn btn-outline-light btn-lg">Booking Saya</a>
          @if(!$isLoggedIn)
            <a href="/login" class="btn btn-warning btn-lg">Login</a>
          @endif
        </div>

        <div class="mt-3 small cg-muted">
          Klik “Booking Tempat Ini” → jika belum login, otomatis diarahkan login dulu lalu kembali.
        </div>
      </div>

      <div class="col-lg-5">
        <div class="row g-3">
          <div class="col-6">
            <div class="cg-glass rounded-4 p-3 h-100">
              <div class="small cg-muted">Total Tempat</div>
              <div class="display-6 fw-bold mb-0">{{ $totalTempat }}</div>
            </div>
          </div>
          <div class="col-6">
            <div class="cg-glass rounded-4 p-3 h-100">
              <div class="small cg-muted">Tersedia</div>
              <div class="display-6 fw-bold mb-0">{{ $totalTersedia }}</div>
            </div>
          </div>
          <div class="col-12">
            <div class="cg-glass rounded-4 p-3">
              <div class="small cg-muted">Range Harga / Malam</div>
              <div class="fw-semibold fs-4 mb-0">
                Rp{{ number_format($minHarga) }} - Rp{{ number_format($maxHarga) }}
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

@if(!$isLoggedIn)
  <div class="alert alert-info d-flex align-items-center gap-2">
    <span class="badge text-bg-info">Tips</span>
    <div>Kamu bisa lihat-lihat dulu. Untuk booking, kamu akan diminta <a href="/login" class="alert-link">login</a>.</div>
  </div>
@endif

{{-- SLIDER NAV (tabs style) --}}
<div class="d-flex justify-content-between align-items-center mb-2">
  <div>
    <h5 class="mb-0 cg-section-title">Jelajahi</h5>
    <div class="text-muted small">Geser/klik panah untuk pindah halaman.</div>
  </div>

  <div class="btn-group" role="group" aria-label="Navigate slider">
    <a class="btn btn-outline-dark btn-sm {{ $tab==='tempat' ? 'active' : '' }}" href="/?tab=tempat">Tempat</a>
    <a class="btn btn-outline-dark btn-sm {{ $tab==='fasilitas' ? 'active' : '' }}" href="/?tab=fasilitas">Fasilitas</a>
  </div>
</div>

{{-- CONTENT BY TAB (NO CAROUSEL, NO NYASAR) --}}
@if($tab === 'tempat')
  <div class="card shadow-sm mb-4">
    <div class="card-body">

      <form class="row g-2 align-items-end" method="GET" action="/">
        <input type="hidden" name="tab" value="tempat">

        <div class="col-md-5">
          <label class="form-label mb-1">Cari tempat</label>
          <input name="q" value="{{ $q }}" class="form-control">
        </div>

        <div class="col-md-3">
          <label class="form-label mb-1">Status</label>
          <select name="status" class="form-select">
            <option value="ALL" @selected($statusFilter==='ALL')>Semua</option>
            <option value="TERSEDIA" @selected($statusFilter==='TERSEDIA')>Tersedia</option>
            <option value="BOOKED" @selected($statusFilter==='BOOKED')>Booked</option>
          </select>
        </div>

        <div class="col-md-2">
          <label class="form-label mb-1">Urutkan</label>
          <select name="sort" class="form-select">
            <option value="newest" @selected($sort==='newest')>Terbaru</option>
            <option value="cheapest" @selected($sort==='cheapest')>Termurah</option>
            <option value="priciest" @selected($sort==='priciest')>Termahal</option>
            <option value="name" @selected($sort==='name')>Nama A-Z</option>
          </select>
        </div>

        <div class="col-md-2 d-flex gap-2">
          <button class="btn btn-dark w-100" type="submit">Terapkan</button>
          <a href="/?tab=tempat" class="btn btn-outline-secondary w-100">Reset</a>
        </div>

        <div class="text-muted small mt-2">
          Menampilkan {{ $sorted->count() }} dari {{ $totalTempat }} tempat.
        </div>
      </form>

    </div>
  </div>

  @if($sorted->count() === 0)
    <div class="alert alert-secondary">Tidak ada tempat yang cocok dengan filter saat ini.</div>
  @else
    <div class="row g-3">
      @foreach($sorted as $t)
        @php
          $status = strtoupper((string)($t->STATUS ?? ''));
          $available = ($status === 'TERSEDIA');
          $harga = (float)($t->HARGA_PER_MALAM ?? 0);
        @endphp

        <div class="col-sm-6 col-lg-4">
          <div class="card cg-card h-100 shadow-sm border-0 overflow-hidden">
            @if(!empty($t->FOTO_TEMPAT))
              <a href="#" data-bs-toggle="modal" data-bs-target="#modalTempat{{ $t->ID_TEMPAT }}" class="text-decoration-none">
                <img src="{{ asset('storage/'.$t->FOTO_TEMPAT) }}" class="w-100 cg-img" alt="Foto {{ $t->NAMA_TEMPAT }}">
              </a>
            @else
              <div class="bg-secondary-subtle d-flex align-items-center justify-content-center cg-img">
                <span class="text-muted">Tidak ada foto</span>
              </div>
            @endif

            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start gap-2">
                <div>
                  <h6 class="mb-1 fw-semibold">{{ $t->NAMA_TEMPAT }}</h6>
                  <div class="text-muted small">Rp{{ number_format($harga) }} / malam</div>
                </div>
                <span class="badge {{ $available ? 'bg-success' : 'bg-secondary' }} cg-badge-pill">
                  {{ $t->STATUS }}
                </span>
              </div>
            </div>

            <div class="card-footer bg-white border-0 pt-0 pb-3 px-3">
              @if($available)
                <a href="/booking/wizard?tempat={{ $t->ID_TEMPAT }}" class="btn btn-primary w-100">Booking Tempat Ini</a>
                <div class="text-muted small mt-2">Wizard 2 step: Tempat → Fasilitas → Simpan.</div>
              @else
                <button class="btn btn-secondary w-100" disabled>Tidak Tersedia</button>
              @endif
            </div>
          </div>
        </div>

        @if(!empty($t->FOTO_TEMPAT))
          <div class="modal fade" id="modalTempat{{ $t->ID_TEMPAT }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">{{ $t->NAMA_TEMPAT }}</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <img src="{{ asset('storage/'.$t->FOTO_TEMPAT) }}" class="img-fluid rounded" alt="Foto {{ $t->NAMA_TEMPAT }}">
                  <div class="mt-3 d-flex justify-content-between align-items-center">
                    <div class="text-muted">Rp{{ number_format($harga) }} / malam</div>
                    <span class="badge {{ $available ? 'bg-success' : 'bg-secondary' }} cg-badge-pill">
                      {{ $t->STATUS }}
                    </span>
                  </div>
                </div>
                <div class="modal-footer">
                  @if($available)
                    <a href="/booking/wizard?tempat={{ $t->ID_TEMPAT }}" class="btn btn-primary">Booking Tempat Ini</a>
                  @endif
                  <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
              </div>
            </div>
          </div>
        @endif
      @endforeach
    </div>
  @endif

@else
  <div class="card shadow-sm">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h5 class="mb-0 cg-section-title">Fasilitas Sewa</h5>
          <div class="text-muted small">Fasilitas dipilih di Step 2 wizard booking (opsional).</div>
        </div>
        <a href="/booking/wizard" class="btn btn-outline-primary btn-sm">Mulai Booking Wizard</a>
      </div>
      <hr>

      <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Nama Fasilitas</th>
              <th style="width:220px;">Harga (per malam)</th>
              <th style="width:220px;">Keterangan</th>
            </tr>
          </thead>
          <tbody>
            @forelse($fasilitasList as $f)
              <tr>
                <td class="fw-semibold">{{ $f['name'] }}</td>
                <td>Rp{{ number_format((float)$f['price']) }}</td>
                <td class="text-muted small">Opsional saat booking</td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="text-center text-muted py-3">Belum ada data fasilitas.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="text-muted small mt-2">dihitung per malam sesuai jumlah malam booking.</div>
    </div>
  </div>
@endif


    {{-- SLIDE 2: FASILITAS --}}
    <div class="carousel-item {{ $tab === 'fasilitas' ? 'active' : '' }}" data-tab="fasilitas">
      <div class="card shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h5 class="mb-0 cg-section-title">Fasilitas Sewa</h5>
              <div class="text-muted small">Fasilitas dipilih di Step 2 wizard booking (opsional).</div>
            </div>
            <a href="/booking/wizard" class="btn btn-outline-primary btn-sm">Mulai Booking Wizard</a>
          </div>
          <hr>

          <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>Nama Fasilitas</th>
                  <th style="width:220px;">Harga (per malam)</th>
                  <th style="width:220px;">Keterangan</th>
                </tr>
              </thead>
              <tbody>
                @forelse($fasilitasList as $f)
                  <tr>
                    <td class="fw-semibold">{{ $f['name'] }}</td>
                    <td>Rp{{ number_format((float)$f['price']) }}</td>
                    <td class="text-muted small">Opsional saat booking</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="3" class="text-center text-muted py-3">Belum ada data fasilitas.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="text-muted small mt-2">
            dihitung per malam sesuai jumlah malam booking.
          </div>
        </div>
      </div>
    </div>

  </div>

  {{-- Carousel controls --}}
  <button class="carousel-control-prev" type="button" data-bs-target="#dashboardSlider" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#dashboardSlider" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>

{{-- Sinkronkan URL ?tab= dengan posisi carousel (mencegah "Terapkan" lompat ke Fasilitas) --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  const slider = document.getElementById('dashboardSlider');
  if (!slider || typeof bootstrap === 'undefined') return;

  const carousel = bootstrap.Carousel.getOrCreateInstance(slider, {
    interval: false,
    ride: false,
    touch: true,
    wrap: false
  });

  function syncCarouselWithUrl() {
    const params = new URLSearchParams(window.location.search);
    const tab = params.get('tab') || 'tempat';
    carousel.to(tab === 'fasilitas' ? 1 : 0);
  }

  // Posisi awal sesuai URL
  syncCarouselWithUrl();

  // Pastikan submit filter tempat selalu membawa tab=tempat
  document.querySelectorAll('form[data-tab="tempat"]').forEach(form => {
    form.addEventListener('submit', () => {
      let input = form.querySelector('input[name="tab"]');
      if (!input) {
        input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'tab';
        form.appendChild(input);
      }
      input.value = 'tempat';
    });
  });
});
</script>

@endsection
