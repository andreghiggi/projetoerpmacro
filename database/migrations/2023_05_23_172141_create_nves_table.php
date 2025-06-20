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
        Schema::create('nves', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->nullable()->constrained('empresas');
            $table->foreignId('natureza_id')->nullable()->constrained('natureza_operacaos');
            $table->integer('funcionario_id')->nullable();
            
            $table->string('emissor_nome', 100);
            $table->string('emissor_cpf_cnpj', 18);
            $table->string('aut_xml', 18)->nullable();
            $table->integer('ambiente');
            $table->integer('crt')->nullable();

            $table->foreignId('cliente_id')->nullable()->constrained('clientes');
            $table->foreignId('fornecedor_id')->nullable()->constrained('fornecedors');
            $table->foreignId('caixa_id')->nullable()->constrained('caixas');

            $table->foreignId('transportadora_id')->nullable()->constrained('transportadoras');

            $table->string('chave', 44);
            $table->string('chave_importada', 44)->nullable();
            $table->string('recibo', 30)->nullable();
            $table->string('numero_serie', 3);
            $table->integer('numero');
            $table->integer('numero_sequencial')->nullable();

            $table->integer('sequencia_cce')->default(0);
            $table->string('motivo_rejeicao', 200)->nullable();

            $table->enum('estado', ['novo', 'rejeitado', 'cancelado', 'aprovado']);

            $table->decimal('total', 12, 2);

            $table->decimal('valor_produtos', 12, 2)->nullable();
            $table->decimal('valor_frete', 12, 2)->nullable();

            $table->decimal('desconto', 12, 2)->nullable();
            $table->decimal('acrescimo', 12, 2)->nullable();
            $table->string('observacao', 255)->nullable();

            $table->string('placa', 9)->nullable();
            $table->string('uf', 2)->nullable();
            $table->integer('tipo')->nullable();
            $table->integer('qtd_volumes')->nullable();
            $table->string('numeracao_volumes', 20)->nullable();
            $table->string('especie', 20)->nullable();
            $table->decimal('peso_liquido', 8, 3)->nullable();
            $table->decimal('peso_bruto', 8, 3)->nullable();

            $table->boolean('api')->default(0);
            $table->boolean('gerar_conta_receber')->default(0);
            $table->boolean('gerar_conta_pagar')->default(0);

            $table->string('referencia', 44)->nullable();
            $table->integer('tpNF')->default(1);
            $table->integer('tpEmis')->default(1);
            $table->integer('finNFe')->default(1);
            $table->timestamp('data_emissao')->nullable();

            $table->boolean('orcamento')->default(0);
            $table->integer('ref_orcamento')->nullable();

            $table->date('data_emissao_saida')->nullable();
            $table->date('data_emissao_retroativa')->nullable();
            $table->date('data_entrega')->nullable();

            $table->string('bandeira_cartao', 2)->nullable();
            $table->string('cnpj_cartao', 18)->nullable();
            $table->string('cAut_cartao', 20)->nullable();

            $table->string('tipo_pagamento', 2)->nullable();
            $table->integer('local_id')->nullable();
            $table->text('signed_xml')->nullable();
            $table->integer('user_id')->nullable();
            $table->boolean('contigencia')->default(0);
            
            // alter table nves add column referencia varchar(44) default null;
            // alter table nves add column chave_importada varchar(44) default null;
            // alter table nves add column tpNF integer default null;
            // alter table nves add column tpEmis integer default null;
            // alter table nves add column finNFe integer default null;
            // alter table nves add column data_emissao timestamp default CURRENT_TIMESTAMP;
            
            // alter table nves add column gerar_conta_receber boolean default 0;
            // alter table nves add column gerar_conta_pagar boolean default 0;

            // alter table nves add column orcamento boolean default 0;
            // alter table nves add column ref_orcamento integer default null;

            // alter table nves add column data_emissao_saida date default null;
            // alter table nves add column data_emissao_retroativa date default null;
            // alter table nves add column data_entrega date default null;

            // alter table nves add column bandeira_cartao varchar(2) default null;
            // alter table nves add column cnpj_cartao varchar(18) default null;
            // alter table nves add column cAut_cartao varchar(18) default null;

            // alter table nves add column tipo_pagamento varchar(2) default '';
            // alter table nves add column numero_sequencial integer default null;
            // alter table nves add column crt integer default null;
            // alter table nves add column local_id integer default null;
            // alter table nves add column signed_xml text default null;
            // alter table nves add column user_id integer default null;
            // alter table nves add column contigencia boolean default 0;    
            // alter table nves modify column observacao varchar(255) default null;    
            // alter table nves add column funcionario_id integer default null;
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nves');
    }
};
