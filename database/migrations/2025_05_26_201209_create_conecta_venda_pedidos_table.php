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
        Schema::create('conecta_venda_pedidos', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->unsignedBigInteger('empresa_id')->index();
            $table->string('situacao')->nullable();
            $table->string('comprador')->nullable();
            $table->string('vendedor')->nullable();
            $table->string('vendedor_id')->nullable();
            $table->unsignedBigInteger('nfe_id')->nullable();
            $table->string('catalogo')->nullable();
            $table->string('tabela')->nullable();
            $table->string('email')->nullable();
            $table->string('telefone')->nullable();
            $table->text('observacao')->nullable();
            $table->string('razao_social')->nullable();
            $table->string('inscricao_estadual')->nullable();
            $table->string('cpf')->nullable();
            $table->string('cnpj')->nullable();
            $table->string('cep')->nullable();
            $table->string('estado')->nullable();
            $table->string('cidade')->nullable();
            $table->string('endereco')->nullable();
            $table->string('numero')->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro')->nullable();
            $table->timestamp('data_criacao')->nullable();
            $table->decimal('indice_catalogo', 10, 2)->nullable();
            $table->decimal('valor_pedido', 10, 2)->nullable();
            $table->decimal('valor_frete', 10, 2)->nullable();
            $table->string('frete_tipo')->nullable();
            $table->decimal('desconto', 10, 2)->nullable();
            $table->decimal('valor_desconto', 10, 2)->nullable();
            $table->decimal('valor_pagamento', 10, 2)->nullable();
            $table->string('pagamento_intermediador')->nullable();
            $table->string('pagamento_tipo')->nullable();
            $table->integer('parcelas')->nullable();
            $table->timestamp('data_atualizacao_status')->nullable();
            $table->unsignedBigInteger('cliente_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conecta_venda_pedidos');
    }
};
