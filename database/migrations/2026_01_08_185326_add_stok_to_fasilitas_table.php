<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('booking_fasilitas', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('ID_BOOKING'); // sesuaikan kalau booking pakai ID_BOOKING / id
        $table->unsignedInteger('ID_FASILITAS');
        $table->integer('QTY')->default(0);
        $table->bigInteger('HARGA')->default(0); // simpan harga saat itu
        $table->timestamps();

        $table->index(['ID_BOOKING']);
        $table->index(['ID_FASILITAS']);
    });
}


};
