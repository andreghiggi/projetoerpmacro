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
        Schema::create('servicos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->integer('numero_sequencial')->nullable();
            
            $table->string('nome', 60);
            $table->decimal('valor', 10,2);
            $table->string('unidade_cobranca', 5);
            $table->integer('tempo_servico');

            $table->integer('tempo_adicional')->default(0);
            $table->integer('tempo_tolerancia')->default(0);
            $table->decimal('valor_adicional', 10,2)->default(0);

            $table->decimal('comissao', 6, 2)->default(0);
            
            $table->foreignId('categoria_id')->constrained('categoria_servicos')->onDelete('cascade');;

            $table->string('codigo_servico', 10)->nullable();
            $table->decimal('aliquota_iss', 6, 2)->nullable();
            $table->decimal('aliquota_pis', 6, 2)->nullable();
            $table->decimal('aliquota_cofins', 6, 2)->nullable();
            $table->decimal('aliquota_inss', 6, 2)->nullable();
            $table->string('imagem', 25)->nullable();
            $table->boolean('status')->default(1);
            $table->boolean('reserva')->default(0);
            $table->boolean('padrao_reserva_nfse')->default(0);
            $table->boolean('marketplace')->default(0);
            $table->string('codigo_tributacao_municipio', 30)->nullable();
            $table->string('hash_delivery', 50)->nullable();
            $table->text('descricao');
            $table->boolean('destaque_marketplace')->nullable();

            $table->decimal('aliquota_ir', 7, 2)->nullable();
            $table->decimal('aliquota_csll', 7, 2)->nullable();
            $table->decimal('valor_deducoes', 16, 7)->nullable();
            $table->decimal('desconto_incondicional', 16, 7)->nullable();
            $table->decimal('desconto_condicional', 16, 7)->nullable();
            $table->decimal('outras_retencoes', 16, 7)->nullable();
            $table->string('estado_local_prestacao_servico', 2)->nullable();
            $table->string('natureza_operacao', 100)->nullable();
            $table->string('codigo_cnae', 30)->nullable();

            // alter table servicos add column codigo_cnae varchar(30) default null;
            // alter table servicos add column natureza_operacao varchar(100) default null;
            // alter table servicos add column estado_local_prestacao_servico varchar(2) default null;
            // alter table servicos add column aliquota_ir decimal(7,2) default null;
            // alter table servicos add column aliquota_csll decimal(7,2) default null;
            // alter table servicos add column valor_deducoes decimal(16,7) default null;
            // alter table servicos add column desconto_incondicional decimal(16,7) default null;
            // alter table servicos add column desconto_condicional decimal(16,7) default null;
            // alter table servicos add column outras_retencoes decimal(16,7) default null;


            // alter table servicos add column imagem varchar(25) default null;
            // alter table servicos add column status boolean default 1;
            // alter table servicos add column reserva boolean default 0;
            // alter table servicos add column padrao_reserva_nfse boolean default 0;
            // alter table servicos add column marketplace boolean default 0;
            // alter table servicos add column codigo_tributacao_municipio varchar(30) default null;
            // alter table servicos add column hash_delivery varchar(50) default null;
            // alter table servicos add column descricao text;
            
            // alter table servicos add column destaque_marketplace boolean default null;
            // alter table servicos add column numero_sequencial integer default null;
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicos');
    }
};
