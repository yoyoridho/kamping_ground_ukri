<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pengunjung extends \Illuminate\Foundation\Auth\User
{
    use HasFactory;

    protected $table = 'pengunjung';
    protected $primaryKey = 'ID_PENGUNJUNG';

    public $timestamps = true; 
    // kalau tabel kamu TIDAK punya created_at & updated_at → ganti false

    protected $fillable = [
        'NAMA_PENGUNJUNG',
        'GMAIL',
        'NO_HP_PENGUNJUNG',
        'PASSWORD',
        'email_otp',
        'email_otp_expires_at',
        'email_verified_at',
    ];

    protected $hidden = [
        'PASSWORD',
    ];

    // ini PENTING karena password kamu kolomnya PASSWORD
    public function getAuthPassword()
    {
        return $this->PASSWORD;
    }
}
