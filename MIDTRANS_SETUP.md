Cara integrasi Midtrans Snap (Laravel)

1) Siapkan akun Midtrans (Sandbox)
- Ambil Server Key dan Client Key dari dashboard Midtrans (Sandbox).

2) Set .env
Tambahkan / isi:
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxx
MIDTRANS_IS_PRODUCTION=false

3) Jalankan migration
php artisan migrate

Migration akan menambah kolom midtrans di tabel pembayaran:
MIDTRANS_ORDER_ID, MIDTRANS_SNAP_TOKEN, MIDTRANS_TRANSACTION_STATUS, MIDTRANS_PAYMENT_TYPE, MIDTRANS_RAW_NOTIFICATION

4) Atur URL webhook (Payment Notification URL)
Di Midtrans Dashboard -> Settings -> Configuration:
- Payment Notification URL: https://domain-kamu.com/midtrans/notification

Catatan:
Kalau project jalan di localhost, Midtrans tidak bisa hit webhook ke localhost.
Solusi: pakai ngrok, hosting, atau deploy dulu.

5) Flow di aplikasi
- Setelah booking selesai, buka /booking/{id}/pay
- Halaman payment akan men-generate Snap Token dan tampilkan tombol Bayar sekarang (Midtrans)
- Setelah bayar, status akan update dari webhook menjadi:
  LUNAS / PENDING / GAGAL / EXPIRED

Routes penting:
- GET  /payment/{id}/pay
- POST /payment/{id}/result (opsional, dari callback UI)
- POST /midtrans/notification (webhook)

