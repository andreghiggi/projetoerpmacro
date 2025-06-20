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
        Schema::create('item_nfces', function (Blueprint $table) {
            $table->id();

            $table->foreignId('nfce_id')->nullable()->constrained('nfces');
            $table->foreignId('produto_id')->nullable()->constrained('produtos');
            $table->foreignId('variacao_id')->nullable()->constrained('produto_variacaos');

            $table->decimal('quantidade', 12,4);
            $table->decimal('valor_unitario', 10,2);
            $table->decimal('sub_total', 10,2);

            $table->decimal('perc_icms', 5,2)->default(0);
            $table->decimal('perc_pis', 5,2)->default(0);
            $table->decimal('perc_cofins', 5,2)->default(0);
            $table->decimal('perc_ipi', 5,2)->default(0);

            $table->string('cest', 10)->nullable();
            $table->string('cst_csosn', 3)->default('102');
            $table->string('cst_pis', 3)->default('49');
            $table->string('cst_cofins', 3)->default('49');
            $table->string('cst_ipi', 3)->default('99');

            $table->decimal('perc_red_bc', 5,2)->default(0);
            $table->string('cfop', 4);
            $table->string('ncm', 10);
            
            $table->string('cEnq', 3)->nullable();
            $table->decimal('pST', 10,2)->nullable();
            $table->decimal('vBCSTRet', 10,2)->nullable();
            $table->integer('origem')->default(0);

            $table->string('codigo_beneficio_fiscal', 10)->nullable();

            // alter table item_nfces add column codigo_beneficio_fiscal varchar(10) default null;
            // alter table item_nfces add column variacao_id integer default null;

            // alter table item_nfces modify column quantidade decimal(12,4);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_nfces');
    }
};
