<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1) fasilitas: tambah STOK (default 0) bila belum ada
        if (!Schema::hasColumn('fasilitas', 'STOK')) {
            Schema::table('fasilitas', function (Blueprint $table) {
                $table->integer('STOK')->default(0)->after('HARGA_FASILITAS');
            });
        }

        // 2) barang_kamping: tambah ID_FASILITAS (supaya addon bisa restore stok saat cancel)
        if (!Schema::hasColumn('barang_kamping', 'ID_FASILITAS')) {
            Schema::table('barang_kamping', function (Blueprint $table) {
                $table->unsignedInteger('ID_FASILITAS')->nullable()->after('ID_TIKET');
                $table->index(['ID_FASILITAS']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('barang_kamping', 'ID_FASILITAS')) {
            Schema::table('barang_kamping', function (Blueprint $table) {
                try { $table->dropIndex(['ID_FASILITAS']); } catch (\Throwable $e) {}
                $table->dropColumn('ID_FASILITAS');
            });
        }

        if (Schema::hasColumn('fasilitas', 'STOK')) {
            Schema::table('fasilitas', function (Blueprint $table) {
                $table->dropColumn('STOK');
            });
        }
    }
};
