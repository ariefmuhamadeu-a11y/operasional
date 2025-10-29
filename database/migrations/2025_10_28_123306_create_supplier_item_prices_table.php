
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_item_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            // kolom yang error karena belum ada:
            $table->decimal('last_price', 12, 2)->default(0); // SQLite simpan sebagai REAL; aman
            $table->date('last_date')->nullable();
            $table->timestamps();

            $table->unique(['supplier_id', 'item_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('supplier_item_prices');
    }
};
