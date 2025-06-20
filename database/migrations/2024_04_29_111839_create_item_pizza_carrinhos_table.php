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
        Schema::create('item_pizza_carrinhos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('item_carrinho_id')->constrained('item_carrinho_deliveries');
            $table->foreignId('produto_id')->constrained('produtos');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_pizza_carrinhos');
    }
};
