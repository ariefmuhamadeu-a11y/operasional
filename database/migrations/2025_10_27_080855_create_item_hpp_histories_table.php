<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('item_hpp_histories', function (Blueprint $t) {
            $t->id();
            $t->foreignId('item_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $t->unsignedBigInteger('old_hpp')->nullable();
            $t->unsignedBigInteger('new_hpp');
            $t->string('reason', 120)->nullable();
            $t->string('ref_type', 40)->nullable();
            $t->unsignedBigInteger('ref_id')->nullable();
            $t->timestamp('changed_at')->useCurrent();
            $t->unsignedBigInteger('created_by')->nullable();
            $t->timestamps();

            $t->index(['item_id', 'changed_at']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('item_hpp_histories');
    }
};
