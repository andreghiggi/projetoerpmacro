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
        Schema::create('natureza_operacaos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->nullable()->constrained('empresas');
            $table->string('descricao', 100);

            $table->string('cst_csosn', 3)->nullable();
            $table->string('cst_pis', 3)->nullable();
            $table->string('cst_cofins', 3)->nullable();
            $table->string('cst_ipi', 3)->nullable();

            $table->string('cfop_estadual', 4)->nullable();
            $table->string('cfop_outro_estado', 4)->nullable();
            $table->string('cfop_entrada_estadual', 4)->nullable();
            $table->string('cfop_entrada_outro_estado', 4)->nullable();

            $table->decimal('perc_icms', 5,2)->nullable();
            $table->decimal('perc_pis', 5,2)->nullable();
            $table->decimal('perc_cofins', 5,2)->nullable();
            $table->decimal('perc_ipi', 5,2)->nullable();
            $table->boolean('padrao')->default(0);
            $table->boolean('sobrescrever_cfop')->default(0);
            $table->integer('_id_import')->nullable();

            // alter table natureza_operacaos add column cst_csosn varchar(3) default null;
            // alter table natureza_operacaos add column cst_pis varchar(3) default null;
            // alter table natureza_operacaos add column cst_cofins varchar(3) default null;
            // alter table natureza_operacaos add column cst_ipi varchar(3) default null;
            // alter table natureza_operacaos add column cfop_estadual varchar(4) default null;
            // alter table natureza_operacaos add column cfop_outro_estado varchar(4) default null;
            // alter table natureza_operacaos add column cfop_entrada_estadual varchar(4) default null;
            // alter table natureza_operacaos add column cfop_entrada_outro_estado varchar(4) default null;

            // alter table natureza_operacaos add column perc_icms decimal(5, 2) default null;
            // alter table natureza_operacaos add column perc_pis decimal(5, 2) default null;
            // alter table natureza_operacaos add column perc_cofins decimal(5, 2) default null;
            // alter table natureza_operacaos add column perc_ipi decimal(5, 2) default null;


            // alter table natureza_operacaos modify column perc_icms decimal(5, 2) default null;
            // alter table natureza_operacaos modify column perc_pis decimal(5, 2) default null;
            // alter table natureza_operacaos modify column perc_cofins decimal(5, 2) default null;
            // alter table natureza_operacaos modify column perc_ipi decimal(5, 2) default null;
            // alter table natureza_operacaos add column padrao boolean default 0;
            // alter table natureza_operacaos add column sobrescrever_cfop boolean default 0;
            // alter table natureza_operacaos add column _id_import integer default null;
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('natureza_operacaos');
    }
};
