<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilTiket extends Model
{
    protected $table = 'hasil_tiket';
    protected $primaryKey = 'ID_TEMPAT';
    public $timestamps = false;

protected $fillable = [
  'ID_PEGAWAI',
  'NAMA_TEMPAT',
  'HARGA_PER_MALAM',
  'STATUS',
  'BARCODE',
  'FOTO_TEMPAT',
];

    public function bookings()
    {
    return $this->hasMany(Tiket::class, 'ID_TEMPAT', 'ID_TEMPAT');
    }
    public function tiket()
    {
    return $this->hasMany(\App\Models\Tiket::class, 'ID_TEMPAT', 'ID_TEMPAT');
    }

    public function addons()
    {
    return $this->hasMany(\App\Models\BarangKamping::class, 'ID_TEMPAT', 'ID_TEMPAT');
    }
}
