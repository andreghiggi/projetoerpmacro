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
        Schema::create('lista_precos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas');
            $table->string('nome', 50);
            $table->enum('ajuste_sobre', ['valor_compra', 'valor_venda']);
            $table->enum('tipo', ['incremento', 'reducao']);
            $table->decimal('percentual_alteracao', 5, 2);
            $table->string('tipo_pagamento', 2)->nullable();
            $table->foreignId('funcionario_id')->nullable()->constrained('funcionarios');
            $table->boolean('status')->default(1);
            
            // alter table lista_precos add column status boolean default 1;
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lista_precos');
    }
};
