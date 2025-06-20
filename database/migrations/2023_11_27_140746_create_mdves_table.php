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
        Schema::create('mdves', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas');
            $table->string('uf_inicio', 2);
            $table->string('uf_fim', 2);
            $table->boolean('encerrado');
            $table->date('data_inicio_viagem');
            $table->boolean('carga_posterior');
            $table->string('cnpj_contratante', 18);

            $table->foreignId('veiculo_tracao_id')->nullable()->constrained('veiculos');
            $table->foreignId('veiculo_reboque_id')->nullable()->constrained('veiculos');
            $table->foreignId('veiculo_reboque2_id')->nullable()->constrained('veiculos');
            $table->foreignId('veiculo_reboque3_id')->nullable()->constrained('veiculos');
            $table->enum('estado_emissao', ['novo', 'aprovado', 'rejeitado', 'cancelado']);

            $table->integer('mdfe_numero');
            $table->string('chave', 44);
            $table->string('protocolo', 16);

            $table->string('seguradora_nome', 30);
            $table->string('seguradora_cnpj', 18);
            $table->string('numero_apolice', 15);
            $table->string('numero_averbacao', 40);

            $table->decimal('valor_carga', 10, 2);
            $table->decimal('quantidade_carga', 16, 4);
            $table->string('info_complementar', 60);
            $table->string('info_adicional_fisco', 60);

            $table->string('condutor_nome', 60);
            $table->string('condutor_cpf', 15);
            $table->string('lac_rodo', 8);
            $table->integer('tp_emit');
            $table->integer('tp_transp');

            $table->string('produto_pred_nome', 50)->defaul('');
            $table->string('produto_pred_ncm', 8)->defaul('');
            $table->string('produto_pred_cod_barras', 13)->defaul('');
            $table->string('cep_carrega', 8)->defaul('');
            $table->string('cep_descarrega', 8)->defaul('');
            $table->string('tp_carga', 2)->defaul('');

            $table->string('latitude_carregamento', 15)->defaul('');
            $table->string('longitude_carregamento', 15)->defaul('');
            $table->string('latitude_descarregamento', 15)->defaul('');
            $table->string('longitude_descarregamento', 15)->defaul('');
            $table->integer('local_id')->nullable();
            $table->integer('tipo_modal')->default(1);

            // alter table mdves add column latitude_carregamento varchar(15) default '';
            // alter table mdves add column longitude_carregamento varchar(15) default '';
            // alter table mdves add column latitude_descarregamento varchar(15) default '';
            // alter table mdves add column longitude_descarregamento varchar(15) default '';

            // alter table mdves add column estado_emissao enum('novo', 'aprovado', 'rejeitado', 'cancelado');
            // alter table mdves add column mdfe_numero integer default null;

            // alter table mdves add column local_id integer default null;
            // alter table mdves add column tipo_modal integer default 1;
            // alter table mdves modify column quantidade_carga decimal(16, 4) default 0;

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mdves');
    }
};
