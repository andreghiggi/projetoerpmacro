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
        Schema::create('config_gerals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas');
            $table->enum('balanca_valor_peso', ['peso', 'valor']);
            $table->integer('balanca_digito_verificador')->nullable();
            $table->boolean('confirmar_itens_prevenda')->default(0);
            $table->boolean('gerenciar_estoque')->default(0);
            $table->boolean('agrupar_itens')->default(0);
            $table->text('notificacoes');
            $table->decimal('margem_combo', 5,2)->default(50);
            $table->decimal('percentual_desconto_orcamento', 5,2)->nullable();
            $table->decimal('percentual_lucro_produto', 10,2)->default(0);

            $table->text('tipos_pagamento_pdv');
            $table->string('senha_manipula_valor', 20)->nullable();
            $table->boolean('abrir_modal_cartao')->default(1);
            $table->enum('tipo_comissao', ['percentual_vendedor', 'percentual_margem'])->nullable();
            $table->string('modelo', 20)->default('light');
            $table->boolean('alerta_sonoro')->default(1);
            $table->boolean('cabecalho_pdv')->default(1);
            $table->boolean('definir_vendedor_pdv')->default(0);
            $table->boolean('gerar_conta_receber_padrao')->default(1);
            $table->integer('regime_nfse')->nullable();

            $table->string('mercadopago_public_key_pix', 120)->nullable();
            $table->string('mercadopago_access_token_pix', 120)->nullable();

            $table->boolean('definir_vendedor_pdv_off')->default(0);
            $table->string('acessos_pdv_off', 255)->nullable();
            $table->enum('tipo_menu', ['vertical', 'horizontal'])->default('vertical');
            $table->enum('cor_menu', ['light', 'brand', 'dark'])->default('light');
            $table->enum('cor_top_bar', ['light', 'brand', 'dark'])->default('light');
            $table->boolean('usar_ibpt')->default(1);
            $table->integer('casas_decimais_quantidade')->default(2);
            $table->integer('cliente_padrao_pdv_off')->default(null);

            $table->text('mensagem_padrao_impressao_venda');
            $table->text('mensagem_padrao_impressao_os');

            $table->integer('ultimo_codigo_produto')->default(0);
            $table->integer('ultimo_codigo_cliente')->default(0);
            $table->integer('ultimo_codigo_fornecedor')->default(0);
            $table->boolean('app_valor_aprazo')->default(0);
            $table->boolean('impressao_sem_janela_cupom')->default(0);

            // alter table config_gerals add column mensagem_padrao_impressao_venda text;
            // alter table config_gerals add column mensagem_padrao_impressao_os text;

            // alter table config_gerals add column confirmar_itens_prevenda boolean default 0;
            // alter table config_gerals modify column balanca_digito_verificador integer default null;
            // alter table config_gerals add column notificacoes text;
            // alter table config_gerals add column margem_combo decimal(5,2) default 50;
            // alter table config_gerals add column gerenciar_estoque boolean default 0;
            // alter table config_gerals add column percentual_lucro_produto decimal(10,2) default 0;
            // alter table config_gerals add column tipos_pagamento_pdv text;
            // alter table config_gerals add column senha_manipula_valor varchar(20) default null;
            // alter table config_gerals add column abrir_modal_cartao boolean default 1;

            // alter table config_gerals add column percentual_desconto_orcamento decimal(5,2) default null;
            // alter table config_gerals add column agrupar_itens boolean default 0;
            // alter table config_gerals add column alerta_sonoro boolean default 0;
            // alter table config_gerals add column tipo_comissao enum('percentual_vendedor', 'percentual_margem') default 'percentual_vendedor';

            // alter table config_gerals add column modelo varchar(20) default 'light';
            // alter table config_gerals add column cabecalho_pdv boolean default 1;
            // alter table config_gerals add column regime_nfse integer default null;

            // alter table config_gerals add column mercadopago_public_key_pix varchar(120) default null;
            // alter table config_gerals add column mercadopago_access_token_pix varchar(120) default null;

            // alter table config_gerals add column definir_vendedor_pdv_off boolean default 0;
            // alter table config_gerals add column definir_vendedor_pdv boolean default 0;
            // alter table config_gerals add column acessos_pdv_off varchar(255) default null;
            // alter table config_gerals add column tipo_menu enum('vertical', 'horizontal') default 'light';
            // alter table config_gerals add column cor_menu enum('light', 'brand', 'dark') default 'light';
            // alter table config_gerals add column cor_top_bar enum('light', 'brand', 'dark') default 'light';
            // alter table config_gerals add column usar_ibpt boolean default 1;
            // alter table config_gerals add column casas_decimais_quantidade integer default 2;
            // alter table config_gerals add column cliente_padrao_pdv_off integer default null;

            // alter table config_gerals add column gerar_conta_receber_padrao boolean default 1;

            // alter table config_gerals add column ultimo_codigo_produto integer default 0;
            // alter table config_gerals add column ultimo_codigo_cliente integer default 0;
            // alter table config_gerals add column ultimo_codigo_fornecedor integer default 0;

            // alter table config_gerals add column app_valor_aprazo boolean default 0;
            // alter table config_gerals add column impressao_sem_janela_cupom boolean default 0;


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('config_gerals');
    }
};
