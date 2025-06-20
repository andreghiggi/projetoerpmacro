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
        Schema::create('notificao_cardapios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('pedido_id')->nullable()->constrained('pedidos');
            $table->string('mesa', 20)->nullable();
            $table->string('comanda', 20)->nullable();

            $table->enum('tipo', ['garcom', 'fechar_mesa']);
            $table->string('tipo_pagamento', 2)->nullable();

            $table->integer('avaliacao')->nullable();
            $table->string('observacao', 150)->nullable();
            $table->boolean('status')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificao_cardapios');
    }
};
