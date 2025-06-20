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
        Schema::create('item_nota_servicos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('nota_servico_id')->nullable()->constrained('nota_servicos');
            $table->foreignId('servico_id')->nullable()->constrained('servicos');

            $table->text('discriminacao');
            $table->decimal('valor_servico', 16, 7);

            $table->string('codigo_cnae', 30)->nullable();
            $table->string('codigo_servico', 30)->nullable();
            $table->string('codigo_tributacao_municipio', 30)->nullable();
            $table->integer('exigibilidade_iss');
            $table->integer('iss_retido');
            $table->date('data_competencia')->nullable();
            $table->string('estado_local_prestacao_servico', 2)->nullable();
            $table->string('cidade_local_prestacao_servico', 60)->nullable();

            $table->decimal('valor_deducoes', 16, 7)->nullable();
            $table->decimal('desconto_incondicional', 16, 7)->nullable();
            $table->decimal('desconto_condicional', 16, 7)->nullable();
            $table->decimal('outras_retencoes', 16, 7)->nullable();
            $table->decimal('aliquota_iss', 7, 2)->nullable();
            $table->decimal('aliquota_pis', 7, 2)->nullable();
            $table->decimal('aliquota_cofins', 7, 2)->nullable();
            $table->decimal('aliquota_inss', 7, 2)->nullable();
            $table->decimal('aliquota_ir', 7, 2)->nullable();
            $table->decimal('aliquota_csll', 7, 2)->nullable();

            $table->enum('intermediador', ['n', 'f', 'j'])->nullable();
            $table->string('documento_intermediador', 18)->nullable();
            $table->string('nome_intermediador', 80)->nullable();
            $table->string('im_intermediador', 20)->nullable();

            $table->integer('responsavel_retencao_iss')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_nota_servicos');
    }
};
