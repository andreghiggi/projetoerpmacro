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
        Schema::create('nuvem_shop_pedidos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas');

            $table->string('pedido_id', 30);
            $table->string('rua', 80);
            $table->string('numero', 80);
            $table->string('bairro', 50);
            $table->string('cidade', 40);
            $table->string('cep', 10);

            $table->decimal('subtotal', 10, 2);
            $table->decimal('total', 10, 2);
            $table->decimal('valor_frete', 10, 2);
            $table->decimal('desconto', 10, 2);

            $table->string('observacao', 150)->nullable();

            $table->string('cliente_id', 30);
            $table->string('nome', 50);
            $table->string('email', 50);
            $table->string('documento', 20);

            $table->integer('nfe_id')->nullable();
            $table->string('status_envio', 20);
            $table->string('gateway', 30);
            $table->string('status_pagamento', 30);
            $table->string('data', 30);

            $table->integer('venda_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nuvem_shop_pedidos');
    }
};
