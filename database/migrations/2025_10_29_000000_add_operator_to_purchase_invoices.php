<?php

// database/migrations/2025_10_29_000000_add_operator_to_purchase_invoices.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_invoices', function (Blueprint $t) {
            $t->foreignId('operator_id')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete()
                ->after('supplier_id');
        });
    }
    public function down(): void
    {
        Schema::table('purchase_invoices', function (Blueprint $t) {
            $t->dropConstrainedForeignId('operator_id');
        });
    }
};
