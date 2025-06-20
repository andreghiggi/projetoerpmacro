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
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();

            $table->string('nome', 100);
            $table->string('nome_fantasia', 100)->nullable();
            $table->string('cpf_cnpj', 18);
            $table->string('aut_xml', 18)->nullable();
            $table->string('ie', 18)->nullable();

            $table->string('email', 60)->nullable();
            $table->string('celular', 20)->nullable();

            $table->binary('arquivo')->nullable();
            $table->string('senha', 30)->nullable();
            $table->boolean('status')->default(1);

            $table->string('cep', 9)->nullable();
            $table->string('rua', 50)->nullable();
            $table->string('numero', 10)->nullable();
            $table->string('bairro', 30)->nullable();
            $table->string('complemento', 50)->nullable();

            $table->foreignId('cidade_id')->nullable()->constrained('cidades');

            $table->integer('natureza_id_pdv')->nullable();
            $table->integer('numero_ultima_nfe_producao')->nullable();
            $table->integer('numero_ultima_nfe_homologacao')->nullable();
            $table->integer('numero_serie_nfe')->nullable();

            $table->integer('numero_ultima_nfce_producao')->nullable();
            $table->integer('numero_ultima_nfce_homologacao')->nullable();
            $table->integer('numero_serie_nfce')->nullable();

            $table->integer('numero_ultima_cte_producao')->nullable();
            $table->integer('numero_ultima_cte_homologacao')->nullable();
            $table->integer('numero_serie_cte')->nullable();

            $table->integer('numero_ultima_mdfe_producao')->nullable();
            $table->integer('numero_ultima_mdfe_homologacao')->nullable();
            $table->integer('numero_serie_mdfe')->nullable();

            $table->integer('numero_ultima_nfse')->nullable();
            $table->integer('numero_serie_nfse')->nullable();

            $table->string('csc', 50)->nullable();
            $table->string('csc_id', 20)->nullable();

            $table->integer('ambiente');

            $table->enum('tributacao', ['MEI', 'Simples Nacional', 'Regime Normal', 'Simples Nacional, excesso sublimite de receita bruta']);
            $table->string('token', 50)->nullable();
            $table->string('token_nfse', 200)->nullable();
            $table->string('logo', 100)->default('');

            $table->boolean('tipo_contador')->default(0);
            $table->integer('limite_cadastro_empresas')->default(0);
            $table->decimal('percentual_comissao', 10, 2)->default(0);
            $table->decimal('perc_ap_cred', 10, 2)->default(0);
            $table->integer('empresa_selecionada')->nullable();
            $table->boolean('exclusao_icms_pis_cofins')->default(0);
            
            $table->text('observacao_padrao_nfe');
            $table->text('observacao_padrao_nfce');
            $table->text('mensagem_aproveitamento_credito');

            $table->integer('dia_vencimento_boleto')->nullable();
            $table->boolean('receber_com_boleto')->default(0);
            $table->string('asaas_id', 20)->nullable();

            // alter table empresas add column natureza_id_pdv integer default null;
            // alter table empresas add column numero_ultima_mdfe_producao integer default null;
            // alter table empresas add column numero_ultima_mdfe_homologacao integer default null;
            // alter table empresas add column numero_serie_mdfe integer default null;
            // alter table empresas add column logo varchar(100) default null;
            // alter table empresas add column tipo_contador boolean default 0;
            // alter table empresas add column exclusao_icms_pis_cofins boolean default 0;
            // alter table empresas add column limite_cadastro_empresas integer default 0;
            // alter table empresas add column percentual_comissao decimal(10,2) default 0;
            // alter table empresas add column empresa_selecionada integer default null;
            // alter table empresas add column token_nfse varchar(200) default null;

            // alter table empresas add column numero_ultima_nfse integer default null;
            // alter table empresas add column numero_serie_nfse integer default null;
            // alter table empresas add column aut_xml varchar(18) default null;
            // alter table empresas modify column tributacao enum('MEI', 'Simples Nacional', 'Regime Normal', 'Simples Nacional, excesso sublimite de receita bruta');

            // alter table empresas add column observacao_padrao_nfe text;
            // alter table empresas add column observacao_padrao_nfce text;
            // alter table empresas add column mensagem_aproveitamento_credito text;
            
            // alter table empresas add column perc_ap_cred decimal(10,2) default 0;

            // alter table empresas add column receber_com_boleto boolean default 0;
            // alter table empresas add column dia_vencimento_boleto integer default null;
            // alter table empresas add column asaas_id varchar(20) default null;

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
