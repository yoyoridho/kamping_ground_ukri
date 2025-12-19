<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tiket extends Model
{
    protected $table = 'tiket';
    protected $primaryKey = 'ID_TIKET';
    public $timestamps = false;

    protected $fillable = [
        'ID_PENGUNJUNG',
        'ID_TEMPAT',
        'TANGGAL_MULAI',
        'TANGGAL_SELESAI',
        'JUMLAH_ORANG',
    ];

    public function pengunjung()
    {
        return $this->belongsTo(Pengunjung::class, 'ID_PENGUNJUNG', 'ID_PENGUNJUNG');
    }

    public function tempat()
    {
        return $this->belongsTo(HasilTiket::class, 'ID_TEMPAT', 'ID_TEMPAT');
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'ID_TIKET', 'ID_TIKET');
    }

    public function addons()
    {
        return $this->hasMany(BarangKamping::class, 'ID_TIKET', 'ID_TIKET');
    }
}
