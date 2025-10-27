<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('material_types', function (Blueprint $t) {
            $t->id();
            $t->string('code', 32)->unique();  // FLEECE280, RIB2X2, KATUN24S
            $t->string('name', 120);           // Fleece 280, Rib 2x2, Katun Combed 24s
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('material_types');
    }
};
