<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('hasil_tiket', function (Blueprint $table) {
            if (!Schema::hasColumn('hasil_tiket', 'FOTO_TEMPAT')) {
                $table->string('FOTO_TEMPAT', 255)->nullable()->after('BARCODE');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hasil_tiket', function (Blueprint $table) {
            if (Schema::hasColumn('hasil_tiket', 'FOTO_TEMPAT')) {
                $table->dropColumn('FOTO_TEMPAT');
            }
        });
    }
};
