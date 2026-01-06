<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangKamping extends Model
{
    protected $table = 'barang_kamping';
    protected $primaryKey = 'ID_BARANG';
    public $timestamps = false;

    protected $fillable = [
        'ID_PENGUNJUNG',
        'ID_TEMPAT',
        'ID_TIKET',
        'NAMA_BARANG',
        'HARGA_BARANG',
        'QTY',
    ];

public function tiket()
{
    return $this->belongsTo(\App\Models\Tiket::class, 'ID_TIKET', 'ID_TIKET');
}

public function pengunjung()
{
    return $this->belongsTo(\App\Models\Pengunjung::class, 'ID_PENGUNJUNG', 'ID_PENGUNJUNG');
}

public function tempat()
{
    return $this->belongsTo(\App\Models\HasilTiket::class, 'ID_TEMPAT', 'ID_TEMPAT');
}
}
