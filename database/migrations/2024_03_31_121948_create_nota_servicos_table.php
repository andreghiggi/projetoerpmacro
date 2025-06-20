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
        Schema::create('nota_servicos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->nullable()->constrained('empresas');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes');
            $table->foreignId('cidade_id')->nullable()->constrained('cidades');

            $table->decimal('valor_total', 16, 7);

            $table->boolean('gerar_conta_receber')->default(0);
            $table->date('data_vencimento')->nullable();
            $table->integer('conta_receber_id')->nullable();

            $table->enum('estado', ['novo', 'rejeitado', 'aprovado', 'cancelado', 'processando']);
            $table->string('serie', 3);
            $table->string('codigo_verificacao', 20);
            $table->integer('numero_nfse');

            $table->string('url_xml', 255);
            $table->string('url_pdf_nfse', 255);
            $table->string('url_pdf_rps', 255);

            $table->string('documento', 18);
            $table->string('razao_social', 60);
            $table->string('im', 20)->nullable();
            $table->string('ie', 20)->nullable();
            $table->string('cep', 9);
            $table->string('rua', 80);
            $table->string('numero', 20);
            $table->string('bairro', 40);
            $table->string('complemento', 80)->nullable();

            $table->string('email', 80)->nullable();
            $table->string('telefone', 20)->nullable();

            $table->string('natureza_operacao', 100)->nullable();
            $table->string('uuid', 100)->nullable();
            $table->string('chave', 50)->nullable();
            $table->integer('ambiente');

            // alter table nota_servicos add column ambiente integer;
            // alter table nota_servicos add column gerar_conta_receber boolean default null;
            // alter table nota_servicos add column data_vencimento date default null;
            // alter table nota_servicos add column conta_receber_id integer default null;

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nota_servicos');
    }
};
