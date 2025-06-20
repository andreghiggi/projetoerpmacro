<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Type\Integer;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pre_vendas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas');

            $table->foreignId('cliente_id')->nullable()->constrained('clientes');
            $table->integer('lista_id')->nullable();
            $table->foreignId('usuario_id')->constrained('users');
            $table->foreignId('funcionario_id')->nullable()->constrained('funcionarios');
            $table->foreignId('natureza_id')->constrained('natureza_operacaos');

            $table->decimal('valor_total', 16,7);
            $table->decimal('desconto', 10,2);
            $table->decimal('acrescimo', 10,2);

            $table->string('forma_pagamento', 20)->nullable();
            $table->string('tipo_pagamento', 2)->nullable();

            $table->string('observacao', 150);
            $table->integer('pedido_delivery_id')->nullable();

            $table->enum('tipo_finalizado', ['nfe', 'nfce']);

            $table->integer('venda_id')->nullable();
            $table->string('codigo', 8);

            $table->string('bandeira_cartao', 2)->default('99');
            $table->string('cnpj_cartao', 18)->default('');
            $table->string('cAut_cartao', 20)->default('');
            $table->string('descricao_pag_outros', 80)->default('');

            $table->boolean('rascunho')->default(false);

            $table->boolean('status')->default(true);
            $table->integer('local_id')->nullable();
            
            // alter table pre_vendas add column lista_id integer default null;
            // alter table pre_vendas add column local_id integer default null;

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pre_vendas');
    }
};
