<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            if (!Schema::hasColumn('pembayaran', 'MIDTRANS_ORDER_ID')) {
                $table->string('MIDTRANS_ORDER_ID', 64)->nullable()->after('STATUS_BAYAR');
            }
            if (!Schema::hasColumn('pembayaran', 'MIDTRANS_SNAP_TOKEN')) {
                $table->string('MIDTRANS_SNAP_TOKEN', 64)->nullable()->after('MIDTRANS_ORDER_ID');
            }
            if (!Schema::hasColumn('pembayaran', 'MIDTRANS_TRANSACTION_STATUS')) {
                $table->string('MIDTRANS_TRANSACTION_STATUS', 32)->nullable()->after('MIDTRANS_SNAP_TOKEN');
            }
            if (!Schema::hasColumn('pembayaran', 'MIDTRANS_PAYMENT_TYPE')) {
                $table->string('MIDTRANS_PAYMENT_TYPE', 32)->nullable()->after('MIDTRANS_TRANSACTION_STATUS');
            }
            if (!Schema::hasColumn('pembayaran', 'MIDTRANS_RAW_NOTIFICATION')) {
                $table->longText('MIDTRANS_RAW_NOTIFICATION')->nullable()->after('MIDTRANS_PAYMENT_TYPE');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $cols = [
                'MIDTRANS_RAW_NOTIFICATION',
                'MIDTRANS_PAYMENT_TYPE',
                'MIDTRANS_TRANSACTION_STATUS',
                'MIDTRANS_SNAP_TOKEN',
                'MIDTRANS_ORDER_ID',
            ];
            foreach ($cols as $c) {
                if (Schema::hasColumn('pembayaran', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
