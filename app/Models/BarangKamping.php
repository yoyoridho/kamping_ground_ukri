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
}
