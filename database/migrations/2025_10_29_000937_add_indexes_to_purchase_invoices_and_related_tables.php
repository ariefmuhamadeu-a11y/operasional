<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_invoices', function (Blueprint $t) {
            if (!$this->hasIndex('purchase_invoices', 'purchase_invoices_code_index')) {
                $t->index('code');
            }
            if (!$this->hasIndex('purchase_invoices', 'purchase_invoices_supplier_id_index')) {
                $t->index('supplier_id');
            }
            if (!$this->hasIndex('purchase_invoices', 'purchase_invoices_date_index')) {
                $t->index('date');
            }
            if (!$this->hasIndex('purchase_invoices', 'purchase_invoices_status_index')) {
                $t->index('status');
            }
        });

        Schema::table('purchase_invoice_lines', function (Blueprint $t) {
            if (!$this->hasIndex('purchase_invoice_lines', 'pil_purchase_invoice_id_index')) {
                $t->index('purchase_invoice_id', 'pil_purchase_invoice_id_index');
            }
            if (!$this->hasIndex('purchase_invoice_lines', 'pil_item_class_id_index')) {
                $t->index('item_class_id', 'pil_item_class_id_index');
            }
        });

        Schema::table('suppliers', function (Blueprint $t) {
            if (!$this->hasIndex('suppliers', 'suppliers_store_name_index')) {
                $t->index('store_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchase_invoices', function (Blueprint $t) {
            $t->dropIndex(['code']);
            $t->dropIndex(['supplier_id']);
            $t->dropIndex(['date']);
            $t->dropIndex(['status']);
        });
        Schema::table('purchase_invoice_lines', function (Blueprint $t) {
            $t->dropIndex('pil_purchase_invoice_id_index');
            $t->dropIndex('pil_item_class_id_index');
        });
        Schema::table('suppliers', function (Blueprint $t) {
            $t->dropIndex(['store_name']);
        });
    }

    // Helper agar idempotent (hindari error di beberapa DB)
    private function hasIndex(string $table, string $index): bool
    {
        try {
            return Schema::getConnection()->getDoctrineSchemaManager()
                ->listTableDetails($table)
                ->hasIndex($index);
        } catch (\Throwable $e) {
            return false;
        }
    }
};
