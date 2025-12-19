<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Pengunjung extends Authenticatable
{
    protected $table = 'pengunjung';
    protected $primaryKey = 'ID_PENGUNJUNG';
    public $timestamps = false;

    protected $fillable = [
        'NAMA_PENGUNJUNG',
        'PASSWORD',
        'GMAIL',
        'NO_HP_PENGUNJUNG',
    ];

    protected $hidden = ['PASSWORD'];

    public function getAuthPassword() { return $this->PASSWORD; }

    public function tiket()
    {
        return $this->hasMany(Tiket::class, 'ID_PENGUNJUNG', 'ID_PENGUNJUNG');
    }
}
