<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Verifikasi OTP</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body { background: #0b1220; }
    .otp-wrap { min-height: 100vh; display:flex; align-items:center; padding: 24px 0; }
    .glass {
      background: rgba(255,255,255,.08);
      border: 1px solid rgba(255,255,255,.14);
      backdrop-filter: blur(10px);
      border-radius: 18px;
      box-shadow: 0 20px 60px rgba(0,0,0,.35);
    }
    .brand-dot { width:10px; height:10px; border-radius:999px; background:#22c55e; display:inline-block; margin-right:8px; }
    .otp-input {
      width: 52px; height: 56px;
      text-align: center;
      font-size: 20px;
      border-radius: 12px;
      border: 1px solid rgba(255,255,255,.18);
      background: rgba(255,255,255,.06);
      color: #fff;
      outline: none;
    }
    .otp-input:focus {
      border-color: rgba(34,197,94,.8);
      box-shadow: 0 0 0 .2rem rgba(34,197,94,.15);
      background: rgba(255,255,255,.09);
    }
    .muted { color: rgba(255,255,255,.72); }
    .muted-2 { color: rgba(255,255,255,.55); }
    .btn-green {
      background: #22c55e; border: none;
    }
    .btn-green:hover { background:#1fb155; }
    .link-btn {
      background: transparent; border: 0; padding: 0; color: #93c5fd;
      text-decoration: underline; cursor: pointer;
    }
    .link-btn:hover { color: #bfdbfe; }
    .badge-soft {
      background: rgba(255,255,255,.10);
      border: 1px solid rgba(255,255,255,.14);
      color: rgba(255,255,255,.85);
    }
  </style>
</head>
<body>
<div class="otp-wrap">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-md-8 col-lg-5">
        <div class="glass p-4 p-md-5 text-white">

          <div class="d-flex align-items-center gap-2 mb-3">
            <span class="brand-dot"></span>
            <div class="small muted-2">Verifikasi Email</div>
          </div>

          <h4 class="mb-2">Masukkan kode OTP</h4>
          <div class="muted mb-4">
            Kami sudah mengirim kode 6 digit ke email kamu. Kode berlaku 10 menit.
          </div>

          @if ($errors->any())
            <div class="alert alert-danger mb-3">
              {{ $errors->first() }}
            </div>
          @endif

          @if (session('success'))
            <div class="alert alert-success mb-3">
              {{ session('success') }}
            </div>
          @endif

          <form id="otpForm" method="POST" action="{{ route('verify-otp.submit') }}">
            @csrf

            <input type="hidden" name="otp" id="otpHidden" value="">

            <div class="d-flex justify-content-between gap-2 mb-3" dir="ltr">
              @for ($i = 0; $i < 6; $i++)
                <input
                  class="otp-input"
                  type="text"
                  inputmode="numeric"
                  pattern="[0-9]*"
                  maxlength="1"
                  autocomplete="one-time-code"
                  aria-label="Digit OTP {{ $i+1 }}"
                >
              @endfor
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
              <span class="badge badge-soft px-3 py-2 rounded-pill">
                Waktu tersisa: <span id="timer">10:00</span>
              </span>

              <button type="button" class="link-btn" id="clearBtn">hapus</button>
            </div>

            <button class="btn btn-green w-100 py-2 rounded-3" type="submit">
              Verifikasi
            </button>
          </form>

          <div class="mt-4">
            <div class="muted-2 small mb-2">Tidak menerima email?</div>

            <form method="POST" action="{{ route('resend-otp') }}">
              @csrf
              <button class="btn btn-outline-light w-100 rounded-3" type="submit" id="resendBtn">
                Kirim ulang OTP
              </button>
            </form>

            <div class="mt-3 small muted-2">
              Tips: cek folder spam/promotions kalau belum masuk.
            </div>
          </div>

        </div>

        <div class="text-center mt-3 small muted-2">
          <a href="{{ url('/') }}" class="text-decoration-none" style="color: rgba(255,255,255,.55);">
            kembali
          </a>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
  (function () {
    const inputs = Array.from(document.querySelectorAll('.otp-input'));
    const hidden = document.getElementById('otpHidden');
    const form = document.getElementById('otpForm');
    const clearBtn = document.getElementById('clearBtn');

    function syncHidden() {
      hidden.value = inputs.map(i => i.value || '').join('');
    }

    function focusIndex(idx) {
      const el = inputs[idx];
      if (el) el.focus();
    }

    inputs.forEach((input, idx) => {
      input.addEventListener('input', (e) => {
        const v = (input.value || '').replace(/\D/g, '');
        input.value = v.slice(0, 1);
        syncHidden();

        if (input.value && idx < inputs.length - 1) {
          focusIndex(idx + 1);
        }
      });

      input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !input.value && idx > 0) {
          focusIndex(idx - 1);
        }
      });

      input.addEventListener('paste', (e) => {
        const text = (e.clipboardData || window.clipboardData).getData('text');
        const digits = (text || '').replace(/\D/g, '').slice(0, 6).split('');
        if (!digits.length) return;

        e.preventDefault();
        inputs.forEach((i, k) => i.value = digits[k] || '');
        syncHidden();
        const last = Math.min(digits.length, inputs.length) - 1;
        focusIndex(Math.max(0, last));
      });
    });

    clearBtn.addEventListener('click', () => {
      inputs.forEach(i => i.value = '');
      syncHidden();
      focusIndex(0);
    });

    form.addEventListener('submit', (e) => {
      syncHidden();
      if (hidden.value.length !== 6) {
        e.preventDefault();
        alert('OTP harus 6 digit.');
        return;
      }
    });

    // timer UI (display only)
    const timerEl = document.getElementById('timer');
    let seconds = 10 * 60;
    const tick = () => {
      seconds = Math.max(0, seconds - 1);
      const m = String(Math.floor(seconds / 60)).padStart(2, '0');
      const s = String(seconds % 60).padStart(2, '0');
      timerEl.textContent = `${m}:${s}`;
      if (seconds === 0) clearInterval(iv);
    };
    timerEl.textContent = '10:00';
    const iv = setInterval(tick, 1000);

    // autofocus first
    focusIndex(0);
  })();
</script>
</body>
</html>
