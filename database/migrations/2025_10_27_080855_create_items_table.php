<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $t) {
            $t->id();
            $t->string('code', 64)->unique();
            $t->string('name', 150)->nullable();

            $t->foreignId('item_class_id')->constrained()->cascadeOnUpdate()->restrictOnDelete(); // BHBK/ACC/BSJ/BJ
            $t->foreignId('product_category_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete(); // wajib jika BJ

            // HAPUS: $t->foreignId('material_type_id')->nullable()->constrained()...

            $t->string('uom', 16); // BHBK => auto 'KG'
            $t->unsignedBigInteger('current_hpp')->default(0);
            $t->boolean('is_active')->default(true);

            $t->timestamps();

            $t->index(['item_class_id', 'product_category_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
