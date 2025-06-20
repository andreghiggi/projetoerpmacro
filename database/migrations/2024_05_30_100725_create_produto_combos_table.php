<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('produto_combos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('produto_id')->constrained('produtos');
            $table->foreignId('item_id')->constrained('produtos');
            $table->decimal('quantidade', 8,3);
            $table->decimal('valor_compra', 12,4);
            $table->decimal('sub_total', 12,4);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produto_combos');
    }
};
