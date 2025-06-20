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
        Schema::create('trocas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('nfce_id')->constrained('nfces');

            $table->string('observacao', 200)->nullable();
            $table->decimal('valor_troca', 12, 2);
            $table->decimal('valor_original', 12, 2);
            $table->integer('numero_sequencial')->nullable();
            $table->string('codigo', 8);
            $table->string('tipo_pagamento', 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trocas');
    }
};
