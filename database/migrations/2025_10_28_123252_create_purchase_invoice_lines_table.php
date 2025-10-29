<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_invoice_lines', function (Blueprint $t) {
            $t->id();
            $t->foreignId('purchase_invoice_id')->constrained()->cascadeOnDelete();
            $t->foreignId('item_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('item_class_id')->constrained('item_classes'); // BKU/ACC/BSJ/BJD
            $t->string('item_name');
            $t->decimal('qty', 14, 3);
            $t->string('unit', 16)->default('pcs');
            $t->decimal('price', 14, 2);
            $t->decimal('total', 14, 2);
            $t->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('purchase_invoice_lines');
    }
};
