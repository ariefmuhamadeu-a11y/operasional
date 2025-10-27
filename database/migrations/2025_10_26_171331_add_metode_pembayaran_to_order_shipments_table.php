<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('order_shipments', 'metode_pembayaran')) {
            Schema::table('order_shipments', function (Blueprint $table) {
                $table->string('metode_pembayaran')->nullable()->after('some_existing_column');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         // Hapus kolom hanya jika ada (aman untuk SQLite/Laravel 10/11)
        if (Schema::hasColumn('order_shipments', 'metode_pembayaran')) {
            Schema::table('order_shipments', function (Blueprint $table) {
                $table->dropColumn('metode_pembayaran');
            });
        }
    }
};
