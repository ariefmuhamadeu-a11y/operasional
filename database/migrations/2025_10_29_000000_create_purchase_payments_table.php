<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_payments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('purchase_invoice_id')->constrained()->cascadeOnDelete();
            $t->date('date'); // di SQLite akan tersimpan sebagai TEXT (OK)
            $t->string('account', 32)->default('CASH'); // CASH/JAGO/BCA/SEABANK/TRANSFER
            $t->decimal('amount', 14, 2);
            $t->string('note', 200)->nullable();
            $t->timestamps();

            $t->index(['purchase_invoice_id', 'date']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('purchase_payments');
    }
};
