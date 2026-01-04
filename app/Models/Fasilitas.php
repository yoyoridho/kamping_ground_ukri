<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fasilitas extends Model
{
    protected $table = 'fasilitas';
    protected $primaryKey = 'ID_FASILITAS';
    public $timestamps = false;

    protected $fillable = [
        'NAMA_FASILITAS',
        'HARGA_FASILITAS',
        'STATUS',
        'DESKRIPSI',
    ];
}
