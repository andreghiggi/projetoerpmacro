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
        Schema::create('variacao_modelo_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('variacao_modelo_id')->constrained('variacao_modelos');
            $table->string('nome', 100);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variacao_modelo_items');
    }
};
