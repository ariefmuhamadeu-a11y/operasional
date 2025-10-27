<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('item_classes', function (Blueprint $t) {
            $t->id();
            $t->string('code', 12)->unique();   // BHBK, ACC, BSJ, BJ
            $t->string('name', 60);             // Bahan Baku, Aksesoris, ...
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('item_classes');
    }
};
