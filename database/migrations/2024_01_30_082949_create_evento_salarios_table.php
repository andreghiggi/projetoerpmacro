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
        Schema::create('evento_salarios', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 50);
            $table->enum('tipo', ['semanal', 'mensal', 'anual']);
            $table->enum('metodo', ['informado', 'fixo']);
            $table->enum('condicao', ['soma', 'diminui']);
            $table->enum('tipo_valor', ['fixo', 'percentual']);
            $table->boolean('ativo')->default(1);
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evento_salarios');
    }
};
