<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_item_prices', function (Blueprint $t) {
            $t->id();
            $t->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $t->foreignId('item_id')->constrained()->cascadeOnDelete();
            $t->decimal('last_price', 14, 2);
            $t->date('last_date');
            $t->unique(['supplier_id', 'item_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('supplier_item_prices');
    }
};
