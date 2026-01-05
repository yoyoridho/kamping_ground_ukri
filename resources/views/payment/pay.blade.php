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
      @if(empty($clientKey) || empty($snapToken))
        <div class="alert alert-warning mb-0">
          Midtrans belum dikonfigurasi (client key / snap token kosong). Cek .env: MIDTRANS_SERVER_KEY dan MIDTRANS_CLIENT_KEY.
        </div>

        <form method="POST" action="/payment/{{ $pay->ID_PEMBAYARAN }}/simulate" class="d-flex flex-wrap gap-2 mt-3">
          @csrf
          <button name="result" value="success" class="btn btn-success">Simulasikan Sukses</button>
          <button name="result" value="failed" class="btn btn-danger">Simulasikan Gagal</button>
          <button name="result" value="expired" class="btn btn-secondary">Simulasikan Expired</button>
        </form>
      @else
        <button id="pay-button" class="btn btn-primary">Bayar sekarang (Midtrans)</button>
        <div class="text-muted small mt-2">
          Setelah pembayaran, status biasanya akan berubah lewat webhook Midtrans. Kalau kamu masih lokal (xampp/localhost), webhook butuh URL publik (mis. ngrok/hosting).
        </div>
      @endif
    @else
      <div class="alert alert-info mb-0">
        Payment sudah final: <b>{{ $pay->STATUS_BAYAR }}</b>
      </div>
    @endif
  </div>
</div>


@if(!empty($clientKey) && !empty($snapToken) && $pay->STATUS_BAYAR === 'PENDING')
  <script type="text/javascript"
    src="{{ $isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
    data-client-key="{{ $clientKey }}"></script>

  <script>
    (function () {
      const btn = document.getElementById('pay-button');
      if (!btn || !window.snap) return;

      const snapToken = @json($snapToken);
      const csrf = @json(csrf_token());
      const resultUrl = @json('/payment/' . $pay->ID_PEMBAYARAN . '/result');

      function postResult(res) {
        // simpan hasil dari UI (opsional), webhook tetap yang paling valid
        fetch(resultUrl, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf
          },
          body: JSON.stringify({
            order_id: res.order_id,
            transaction_status: res.transaction_status,
            fraud_status: res.fraud_status,
            payment_type: res.payment_type,
            raw: res
          })
        }).catch(() => {});
      }

      btn.addEventListener('click', function () {
        window.snap.pay(snapToken, {
          onSuccess: function (result) {
            postResult(result);
            window.location.href = @json('/booking/' . $pay->ID_TIKET);
          },
          onPending: function (result) {
            postResult(result);
            alert('Pembayaran pending. Silakan selesaikan pembayaran kamu.');
          },
          onError: function (result) {
            postResult(result);
            alert('Pembayaran gagal / error.');
          },
          onClose: function () {
            // user menutup popup
          }
        });
      });
    })();
  </script>
@endif

@endsection
