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
        Schema::create('nfces', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('natureza_id')->nullable()->constrained('natureza_operacaos');
            
            $table->string('emissor_nome', 100);
            $table->string('emissor_cpf_cnpj', 18);
            $table->integer('ambiente');
            $table->integer('lista_id')->nullable();
            $table->integer('funcionario_id')->nullable();

            $table->foreignId('cliente_id')->nullable()->constrained('clientes');
            $table->foreignId('caixa_id')->nullable()->constrained('caixas');

            $table->string('cliente_nome', 100)->nullable();
            $table->string('cliente_cpf_cnpj', 18)->nullable();

            $table->string('chave', 44)->nullable();
            $table->string('chave_sat', 44)->nullable();
            $table->string('recibo', 30)->nullable();
            $table->string('numero_serie', 3);
            $table->integer('numero')->nullable();
            $table->string('motivo_rejeicao', 200)->nullable();

            $table->enum('estado', ['novo', 'rejeitado', 'cancelado', 'aprovado']);
            $table->integer('numero_sequencial')->nullable();

            $table->decimal('total', 12, 2);

            $table->decimal('desconto', 12, 2)->nullable();
            $table->decimal('valor_cashback', 10,2)->nullable();
            $table->decimal('acrescimo', 12, 2)->nullable();
            $table->decimal('valor_entrega', 12, 2)->nullable();
            $table->string('observacao', 200)->nullable();
            $table->boolean('api')->default(0);
            $table->timestamp('data_emissao')->nullable();

            $table->decimal('dinheiro_recebido', 10,2);
            $table->decimal('troco', 10,2);
            $table->string('tipo_pagamento', 2);

            $table->string('bandeira_cartao', 2)->default('99');
            $table->string('cnpj_cartao', 18)->nullable();
            $table->string('cAut_cartao', 20)->nullable();
            $table->boolean('gerar_conta_receber')->default(0);
            $table->integer('local_id')->nullable();
            $table->text('signed_xml')->nullable();
            $table->integer('user_id')->nullable();
            $table->boolean('contigencia')->default(0);
            $table->boolean('reenvio_contigencia')->default(0);
            
            // alter table nfces add column data_emissao timestamp default CURRENT_TIMESTAMP;
            // alter table nfces add column dinheiro_recebido decimal(10, 2) default 0;
            // alter table nfces add column troco decimal(10, 2) default 0;
            // alter table nfces add column valor_entrega decimal(10, 2) default 0;
            // alter table nfces add column tipo_pagamento varchar(2) default '';

            // alter table nfces add column bandeira_cartao varchar(2) default 99;
            // alter table nfces add column cnpj_cartao varchar(18) default null;
            // alter table nfces add column cAut_cartao varchar(18) default null;
            // alter table nfces add column gerar_conta_receber boolean default 0;
            // alter table nfces add column valor_cashback decimal(10,2) default null;
            // alter table nfces add column lista_id integer default null;
            // alter table nfces add column numero_sequencial integer default null;
            // alter table nfces add column funcionario_id integer default null;
            // alter table nfces add column local_id integer default null;
            // alter table nfces add column signed_xml text default null;
            // alter table nfces add column user_id integer default null;

            // alter table nfces add column contigencia boolean default 0;
            // alter table nfces add column reenvio_contigencia boolean default 0;
            // alter table nfces modify column observacao varchar(200) default null;
            // alter table nfces add column chave_sat varchar(44) default null;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nfces');
    }
};
