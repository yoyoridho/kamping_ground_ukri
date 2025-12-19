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
    ];

    public function tiket()
    {
        return $this->belongsTo(Tiket::class, 'ID_TIKET', 'ID_TIKET');
    }
}
