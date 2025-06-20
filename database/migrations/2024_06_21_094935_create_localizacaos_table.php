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
        Schema::create('localizacaos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->nullable()->constrained('empresas');

            $table->string('descricao', 150);
            $table->boolean('status')->default(1);

            $table->string('nome', 150)->nullable();
            $table->string('nome_fantasia', 115000)->nullable();
            $table->string('cpf_cnpj', 18);
            $table->string('aut_xml', 18)->nullable();
            $table->string('ie', 18)->nullable();
            $table->string('email', 60)->nullable();
            $table->string('celular', 20)->nullable();
            $table->binary('arquivo')->nullable();
            $table->string('senha', 30)->nullable();

            $table->string('cep', 9)->nullable();
            $table->string('rua', 50)->nullable();
            $table->string('numero', 10)->nullable();
            $table->string('bairro', 30)->nullable();
            $table->string('complemento', 50)->nullable();

            $table->foreignId('cidade_id')->nullable()->constrained('cidades');

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

            $table->enum('tributacao', ['MEI', 'Simples Nacional', 'Regime Normal']);
            $table->string('token_nfse', 200)->nullable();
            $table->string('logo', 100)->default('');
            $table->decimal('perc_ap_cred', 10, 2)->default(0);
            $table->text('mensagem_aproveitamento_credito');
            $table->string('token_whatsapp', 120)->nullable();
            
            // alter table localizacaos add column perc_ap_cred decimal(10,2) default 0;
            // alter table localizacaos add column mensagem_aproveitamento_credito text;
            // alter table localizacaos add column token_whatsapp varchar(120) default null;

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('localizacaos');
    }
};
