<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $t) {
            $t->id();
            $t->string('code', 16)->unique();
            $t->string('name', 100);
            $t->string('phone', 30)->nullable();

            // role pekerjaan
            $t->enum('role', ['operasional', 'cutting', 'jahit']);

            // skema pembayaran
            $t->enum('payment_type', ['harian', 'per_pcs']);

            // base_rate: fallback opsional (bisa dipakai nanti untuk per-pcs atau harian khusus)
            $t->decimal('base_rate', 12, 2)->nullable();

            $t->boolean('active')->default(true);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
