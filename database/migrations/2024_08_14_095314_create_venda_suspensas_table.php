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
        Schema::create('venda_suspensas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes');
            $table->decimal('total', 12, 2);
            $table->decimal('desconto', 12, 2)->nullable();
            $table->decimal('acrescimo', 12, 2)->nullable();
            $table->string('observacao', 100)->nullable();

            $table->string('tipo_pagamento', 2);
            $table->integer('local_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('funcionario_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venda_suspensas');
    }
};
