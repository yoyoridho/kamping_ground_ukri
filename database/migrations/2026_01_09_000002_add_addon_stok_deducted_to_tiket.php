<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tiket', function (Blueprint $table) {
            if (!Schema::hasColumn('tiket', 'ADDON_STOK_DEDUCTED')) {
                $table->tinyInteger('ADDON_STOK_DEDUCTED')->default(0)->after('JUMLAH_ORANG');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tiket', function (Blueprint $table) {
            if (Schema::hasColumn('tiket', 'ADDON_STOK_DEDUCTED')) {
                $table->dropColumn('ADDON_STOK_DEDUCTED');
            }
        });
    }
};
