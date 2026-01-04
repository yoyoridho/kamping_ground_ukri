<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tiket', function (Blueprint $table) {
            if (!Schema::hasColumn('tiket', 'QR_TOKEN')) {
                $table->string('QR_TOKEN', 80)->nullable();
                $table->unique('QR_TOKEN');
            }

            if (!Schema::hasColumn('tiket', 'CHECKIN_STATUS')) {
                $table->string('CHECKIN_STATUS', 20)->default('BELUM'); // BELUM / SUDAH
            }

            if (!Schema::hasColumn('tiket', 'CHECKIN_AT')) {
                $table->timestamp('CHECKIN_AT')->nullable();
            }

            if (!Schema::hasColumn('tiket', 'CHECKIN_BY')) {
                $table->unsignedInteger('CHECKIN_BY')->nullable(); // ID_PEGAWAI (opsional)
            }
        });
    }

    public function down(): void
    {
        Schema::table('tiket', function (Blueprint $table) {
            if (Schema::hasColumn('tiket', 'QR_TOKEN')) {
                $table->dropUnique(['QR_TOKEN']);
                $table->dropColumn('QR_TOKEN');
            }
            if (Schema::hasColumn('tiket', 'CHECKIN_STATUS')) $table->dropColumn('CHECKIN_STATUS');
            if (Schema::hasColumn('tiket', 'CHECKIN_AT')) $table->dropColumn('CHECKIN_AT');
            if (Schema::hasColumn('tiket', 'CHECKIN_BY')) $table->dropColumn('CHECKIN_BY');
        });
    }
};
