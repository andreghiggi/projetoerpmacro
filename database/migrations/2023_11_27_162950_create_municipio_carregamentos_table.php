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
        Schema::create('municipio_carregamentos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mdfe_id')->constrained('mdves');

            $table->foreignId('cidade_id')->constrained('cidades');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('municipio_carregamentos');
    }
};
