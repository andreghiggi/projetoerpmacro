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
        Schema::create('agendamentos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('funcionario_id')->nullable()->constrained('funcionarios');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes');
            $table->foreignId('empresa_id')->nullable()->constrained('empresas');

            $table->date('data');
            $table->string('observacao', 150)->nullable();
            $table->time('inicio');
            $table->time('termino');

            $table->decimal('total', 10, 2);
            $table->decimal('desconto', 10, 2)->nullable();
            $table->decimal('acrescimo', 10, 2)->nullable();
            $table->decimal('valor_comissao', 10, 2)->default(0);

            $table->boolean('status')->default(0);
            $table->enum('prioridade', ['baixa', 'media', 'alta'])->default('baixa');
            $table->integer('nfce_id')->nullable();
            $table->integer('pedido_delivery_id')->nullable();

            $table->boolean('msg_wpp_manha_horario')->default(0);
            $table->boolean('msg_wpp_alerta_horario')->default(0);


            // alter table agendamentos add column prioridade enum('baixa', 'media', 'alta') default 'baixa';
            // alter table agendamentos add column nfce_id integer default null;
            // alter table agendamentos add column pedido_delivery_id integer default null;

            // alter table agendamentos add column msg_wpp_manha_horario boolean default 0;
            // alter table agendamentos add column msg_wpp_alerta_horario boolean default 0;

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendamentos');
    }
};
