<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';
    protected $primaryKey = 'ID_PEMBAYARAN';
    public $timestamps = false;

    protected $fillable = [
        'ID_TIKET',
        'TANGGAL_PEMBAYARAN',
        'JUMLAH_PEMBAYARAN',
        'METODE_BAYAR',
        'STATUS_BAYAR',
        'BUKTI_PEMBAYARAN',
        'MIDTRANS_ORDER_ID',
        'MIDTRANS_SNAP_TOKEN',
        'MIDTRANS_TRANSACTION_STATUS',
        'MIDTRANS_PAYMENT_TYPE',
        'MIDTRANS_RAW_NOTIFICATION',
    ];

    public function tiket()
    {
        return $this->belongsTo(Tiket::class, 'ID_TIKET', 'ID_TIKET');
    }
}
