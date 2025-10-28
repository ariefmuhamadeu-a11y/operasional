<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_invoices', function (Blueprint $t) {
            $t->id();
            $t->string('code', 40)->unique(); // INV-BKU-YYYYMMDD-###
            $t->foreignId('supplier_id')->constrained();
            $t->date('date');
            $t->date('due_date')->nullable();
            $t->decimal('subtotal', 14, 2)->default(0);
            $t->decimal('other_costs', 14, 2)->default(0); // ongkir/OTENG/dll
            $t->decimal('total', 14, 2)->default(0);
            $t->decimal('paid_total', 14, 2)->default(0);
            $t->enum('status', ['DRAFT', 'TERBIT', 'SEBAGIAN', 'LUNAS'])->default('TERBIT');
            $t->text('note')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('purchase_invoices');
    }
};
