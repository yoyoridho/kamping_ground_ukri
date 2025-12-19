<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Hash;

class PegawaiAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'admin@camping.test';

        Pegawai::updateOrCreate(
            ['EMAIL_PEGAWAI' => $email],
            [
                'NAMA_PEGAWAI' => 'Admin Utama',
                'NO_HP_PEGAWAI' => '081234567890',
                'PASSWORD_PEGAWAI' => Hash::make('admin12345'),
            ]
        );
    }
}
