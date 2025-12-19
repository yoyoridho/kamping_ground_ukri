<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Pegawai extends Authenticatable
{
    protected $table = 'pegawai';
    protected $primaryKey = 'ID_PEGAWAI';
    public $timestamps = false;

    protected $fillable = [
        'NAMA_PEGAWAI',
        'EMAIL_PEGAWAI',
        'NO_HP_PEGAWAI',
        'PASSWORD_PEGAWAI',
    ];

    protected $hidden = ['PASSWORD_PEGAWAI'];

    public function getAuthPassword()
    {
        return $this->PASSWORD_PEGAWAI;
    }
}
