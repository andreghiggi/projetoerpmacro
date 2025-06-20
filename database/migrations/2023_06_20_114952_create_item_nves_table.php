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
        Schema::create('item_nves', function (Blueprint $table) {
            $table->id();

            $table->foreignId('nfe_id')->nullable()->constrained('nves');
            $table->foreignId('produto_id')->nullable()->constrained('produtos');
            $table->foreignId('variacao_id')->nullable()->constrained('produto_variacaos');

            $table->decimal('quantidade', 12,4);
            $table->decimal('valor_unitario', 10,2);
            $table->decimal('sub_total', 10,2);

            $table->decimal('perc_icms', 10,2)->default(0);
            $table->decimal('perc_pis', 10,2)->default(0);
            $table->decimal('perc_cofins', 10,2)->default(0);
            $table->decimal('perc_ipi', 10,2)->default(0);

            $table->string('cst_csosn', 3);
            $table->string('cst_pis', 3);
            $table->string('cst_cofins', 3);
            $table->string('cst_ipi', 3);
            $table->string('cest', 10)->nullable();

            $table->decimal('vbc_icms', 10,2)->default(0);
            $table->decimal('vbc_pis', 10,2)->default(0);
            $table->decimal('vbc_cofins', 10,2)->default(0);
            $table->decimal('vbc_ipi', 10,2)->default(0);

            $table->decimal('perc_red_bc', 10,2)->nullable();

            $table->string('cfop', 4);
            $table->string('ncm', 10);

            $table->string('cEnq', 3)->nullable();
            $table->decimal('pST', 10,2)->nullable();
            $table->decimal('vBCSTRet', 10,2)->nullable();
            $table->integer('origem')->default(0);
            $table->string('codigo_beneficio_fiscal', 10)->nullable();

            $table->string('lote', 30)->nullable();
            $table->date('data_vencimento')->nullable();
            $table->string('xPed', 30)->nullable();
            $table->string('nItemPed', 30)->nullable();

            $table->string('infAdProd', 200)->nullable();

            $table->decimal('pMVAST', 10,4)->nullable();
            $table->decimal('vBCST', 10,2)->nullable();
            $table->decimal('pICMSST', 10,2)->nullable();
            $table->decimal('vICMSST', 10,2)->nullable();
            $table->decimal('vBCFCPST', 10,2)->nullable();
            $table->decimal('pFCPST', 10,2)->nullable();
            $table->decimal('vFCPST', 10,2)->nullable();
            $table->integer('modBCST')->nullable();

            // alter table item_nves add column pMVAST decimal(10,4) default null;
            // alter table item_nves add column vBCST decimal(10,2) default null;
            // alter table item_nves add column pICMSST decimal(10,2) default null;
            // alter table item_nves add column vICMSST decimal(10,2) default null;
            // alter table item_nves add column vBCFCPST decimal(10,2) default null;
            // alter table item_nves add column pFCPST decimal(10,2) default null;
            // alter table item_nves add column vFCPST decimal(10,2) default null;
            // alter table item_nves add column modBCST integer default null;


            // alter table item_nves add column codigo_beneficio_fiscal varchar(10) default null;

            // alter table item_nves add column lote varchar(30) default null;
            // alter table item_nves add column data_vencimento date default null;
            // alter table item_nves add column variacao_id integer default null;

            // alter table item_nves add column vbc_icms decimal(10,2) default 0;
            // alter table item_nves add column vbc_pis decimal(10,2) default 0;
            // alter table item_nves add column vbc_cofins decimal(10,2) default 0;
            // alter table item_nves add column vbc_ipi decimal(10,2) default 0;
            // alter table item_nves add column xPed varchar(30) default null;
            // alter table item_nves add column nItemPed varchar(30) default null;
            // alter table item_nves modify column quantidade decimal(12,4);


            // alter table item_nves modify column infAdProd varchar(200) default null;

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_nves');
    }
};
