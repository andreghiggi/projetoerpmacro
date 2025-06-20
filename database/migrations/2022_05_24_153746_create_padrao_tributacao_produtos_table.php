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
        Schema::create('padrao_tributacao_produtos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->nullable()->constrained('empresas');
            $table->string('descricao', 60);

            $table->decimal('perc_icms', 10,2)->default(0);
            $table->decimal('perc_pis', 10,2)->default(0);
            $table->decimal('perc_cofins', 10,2)->default(0);
            $table->decimal('perc_ipi', 10,2)->default(0);
            $table->string('cst_csosn', 3)->nullable();
            $table->string('cst_pis', 3)->nullable();
            $table->string('cst_cofins', 3)->nullable();
            $table->string('cst_ipi', 3)->nullable();
            $table->string('cfop_estadual', 4);
            $table->string('cfop_outro_estado', 4);
            $table->string('cfop_entrada_estadual', 4)->nullable();
            $table->string('cfop_entrada_outro_estado', 4)->nullable();
            $table->string('cEnq', 3)->nullable();
            $table->decimal('perc_red_bc', 5,2)->nullable();
            $table->decimal('pST', 5,2)->nullable();
            $table->string('cest', 10)->nullable();
            $table->string('ncm', 10)->nullable();
            $table->string('codigo_beneficio_fiscal', 15)->nullable();
            $table->boolean('padrao')->default(0);

            $table->integer('modBCST')->nullable();
            $table->decimal('pMVAST', 5,2)->nullable();
            $table->decimal('pICMSST', 5,2)->nullable();
            $table->decimal('redBCST', 5,2)->nullable();

            // alter table padrao_tributacao_produtos add column codigo_beneficio_fiscal varchar(10) default null;

            // alter table padrao_tributacao_produtos add column cfop_entrada_estadual varchar(4) default null;
            // alter table padrao_tributacao_produtos add column cfop_entrada_outro_estado varchar(4) default null;
            // alter table padrao_tributacao_produtos add column padrao boolean default 0;

            // alter table padrao_tributacao_produtos add column modBCST integer default null;
            // alter table padrao_tributacao_produtos add column pMVAST decimal(5,2) default null;
            // alter table padrao_tributacao_produtos add column pICMSST decimal(5,2) default null;
            // alter table padrao_tributacao_produtos add column redBCST decimal(5,2) default null;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('padrao_tributacao_produtos');
    }
};
