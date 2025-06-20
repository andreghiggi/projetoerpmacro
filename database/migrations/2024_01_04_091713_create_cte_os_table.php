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
        Schema::create('cte_os', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->nullable()->constrained('empresas');

            $table->foreignId('emitente_id')->nullable()->constrained('clientes');
            $table->foreignId('tomador_id')->nullable()->constrained('clientes');
            $table->foreignId('municipio_envio')->nullable()->constrained('cidades');
            $table->foreignId('municipio_inicio')->nullable()->constrained('cidades');
            $table->foreignId('municipio_fim')->nullable()->constrained('cidades');

            $table->foreignId('veiculo_id')->nullable()->constrained('veiculos');

            $table->foreignId('usuario_id')->nullable()->constrained('users');

            $table->string('modal', 2);
            $table->string('cst', 3)->default('00');
            $table->decimal('perc_icms', 5, 2)->default(0);
            $table->decimal('valor_transporte', 10, 2);
            $table->decimal('valor_receber', 10, 2);

            $table->string('descricao_servico', 100)->default('');
            $table->decimal('quantidade_carga', 12, 4);

            $table->foreignId('natureza_id')->nullable()->constrained('natureza_operacaos');

            $table->integer('tomador');
            // Indica o "papel" do tomador: 0-Remetente; 1-Expedidor; 2-Recebedor; 3-Destinatário

            $table->integer('sequencia_cce');
            $table->string('observacao', 200);
            $table->integer('numero_emissao')->default(0);
            $table->string('chave', 48);
            $table->enum('estado_emissao', ['novo', 'aprovado', 'cancelado', 'rejeitado']);
            $table->timestamp('data_emissao')->nullable();

            $table->string('data_viagem', 10);
            $table->string('horario_viagem', 5);

            $table->string('cfop', 4)->nullable();
            $table->string('recibo', 30)->nullable();
            $table->integer('local_id')->nullable();

            // alter table cte_os add column data_viagem varchar(10) default '';
            // alter table cte_os add column horario_viagem varchar(5) default '';

            // alter table cte_os add column cfop varchar(4) default null;
            // alter table cte_os add column recibo varchar(30) default null;
            // alter table cte_os add column local_id integer default null;

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cte_os');
    }
};
