<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1) tiket: tambah ID_TEMPAT supaya booking tahu tempat yang dipilih
        Schema::table('tiket', function (Blueprint $table) {
            if (!Schema::hasColumn('tiket', 'ID_TEMPAT')) {
                $table->integer('ID_TEMPAT')->nullable()->after('ID_PENGUNJUNG');
            }
        });

        // 2) barang_kamping: tambah ID_TIKET + QTY supaya addon nempel ke booking
        Schema::table('barang_kamping', function (Blueprint $table) {
            if (!Schema::hasColumn('barang_kamping', 'ID_TIKET')) {
                $table->integer('ID_TIKET')->nullable()->after('ID_TEMPAT');
            }
            if (!Schema::hasColumn('barang_kamping', 'QTY')) {
                $table->integer('QTY')->default(1)->after('HARGA_BARANG');
            }
        });

        // 3) Foreign key (dibuat terpisah biar aman)
        Schema::table('tiket', function (Blueprint $table) {
            // FK tiket.ID_TEMPAT -> hasil_tiket.ID_TEMPAT
            // NOTE: kalau sudah ada FK manual, skip.
            try {
                $table->foreign('ID_TEMPAT')->references('ID_TEMPAT')->on('hasil_tiket');
            } catch (\Throwable $e) {}
        });

        Schema::table('barang_kamping', function (Blueprint $table) {
            // FK barang_kamping.ID_TIKET -> tiket.ID_TIKET
            try {
                $table->foreign('ID_TIKET')->references('ID_TIKET')->on('tiket');
            } catch (\Throwable $e) {}
        });
    }

    public function down(): void
    {
        Schema::table('barang_kamping', function (Blueprint $table) {
            try { $table->dropForeign(['ID_TIKET']); } catch (\Throwable $e) {}
            if (Schema::hasColumn('barang_kamping', 'ID_TIKET')) $table->dropColumn('ID_TIKET');
            if (Schema::hasColumn('barang_kamping', 'QTY')) $table->dropColumn('QTY');
        });

        Schema::table('tiket', function (Blueprint $table) {
            try { $table->dropForeign(['ID_TEMPAT']); } catch (\Throwable $e) {}
            if (Schema::hasColumn('tiket', 'ID_TEMPAT')) $table->dropColumn('ID_TEMPAT');
        });
    }
};
