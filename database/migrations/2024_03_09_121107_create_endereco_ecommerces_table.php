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
        Schema::create('endereco_ecommerces', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cidade_id')->constrained('cidades');
            $table->foreignId('cliente_id')->constrained('clientes');

            $table->string('rua', 50);
            $table->string('bairro', 30);
            $table->string('numero', 10);
            $table->string('referencia', 50)->nullable();
            $table->string('cep', 10);
        
            $table->boolean('padrao');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('endereco_ecommerces');
    }
};
