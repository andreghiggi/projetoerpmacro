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
        Schema::create('produto_pizza_valors', function (Blueprint $table) {
            $table->id();

            $table->foreignId('produto_id')->constrained('produtos');
            $table->foreignId('tamanho_id')->constrained('tamanho_pizzas');
            $table->decimal('valor', 12, 4);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produto_pizza_valors');
    }
};
