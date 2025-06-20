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
        Schema::create('apuracao_mensal_eventos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('apuracao_id')->nullable()->constrained('apuracao_mensals');
            $table->foreignId('evento_id')->nullable()->constrained('evento_salarios');
            $table->decimal('valor');
            $table->enum('metodo', ['informado', 'fixo']);
            $table->enum('condicao', ['soma', 'diminui']);
            $table->string('nome', 100);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apuracao_mensal_eventos');
    }
};
