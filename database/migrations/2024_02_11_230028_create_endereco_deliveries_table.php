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
        Schema::create('endereco_deliveries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cidade_id')->constrained('cidades');
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('bairro_id')->constrained('bairro_deliveries');

            $table->string('rua', 50);
            $table->string('numero', 10);
            $table->string('referencia', 30)->nullable();
            $table->string('latitude', 10)->nullable();
            $table->string('longitude', 10)->nullable();
            $table->string('cep', 10);
            
            $table->enum('tipo', ['casa', 'trabalho']);
            $table->boolean('padrao');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('endereco_deliveries');
    }
};
