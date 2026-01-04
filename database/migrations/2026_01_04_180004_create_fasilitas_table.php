<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fasilitas', function (Blueprint $table) {
            $table->increments('ID_FASILITAS');
            $table->string('NAMA_FASILITAS', 120);
            $table->unsignedBigInteger('HARGA_FASILITAS');
            $table->string('STATUS', 20)->default('AKTIF'); // AKTIF / NONAKTIF
            $table->string('DESKRIPSI', 255)->nullable();
            // tidak pakai timestamps agar konsisten dengan tabel kamu yang lain
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fasilitas');
    }
};
