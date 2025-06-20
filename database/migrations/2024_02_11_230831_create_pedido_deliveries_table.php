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
        Schema::create('pedido_deliveries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes');

            $table->foreignId('motoboy_id')->nullable()->constrained('motoboys');
            $table->decimal('comissao_motoboy', 10,2)->nullable();

            $table->decimal('valor_total', 10,2);
            $table->decimal('troco_para', 10,2)->nullable();

            $table->string('tipo_pagamento', 20);
            $table->string('observacao', 50)->nullable();
            $table->string('telefone', 15);
            $table->enum('estado', ['novo', 'aprovado', 'cancelado', 'finalizado']);
            $table->string('motivo_estado', 50)->nullable();
            $table->foreignId('endereco_id')->nullable()->constrained('endereco_deliveries');
            $table->foreignId('cupom_id')->nullable()->constrained('cupom_descontos');

            $table->decimal('desconto', 10,2)->nullable();
            $table->decimal('valor_entrega', 10,2);
            $table->boolean('app');
            $table->text('qr_code_base64')->nullable();
            $table->text('qr_code')->nullable();
            $table->string('transacao_id', 50)->nullable();
            $table->string('status_pagamento', 100)->nullable();
            $table->boolean('pedido_lido')->default(0);
            $table->boolean('finalizado')->default(0);
            
            $table->string('horario_cricao', 5)->nullable();
            $table->string('horario_leitura', 5)->nullable();
            $table->string('horario_entrega', 5)->nullable();
            $table->integer('nfce_id')->nullable();

            $table->integer('funcionario_id_agendamento')->nullable();
            $table->string('inicio_agendamento', 5)->nullable();
            $table->string('fim_agendamento', 5)->nullable();
            $table->date('data_agendamento')->nullable();
            
            // alter table pedido_deliveries add column finalizado boolean default 0;
            // alter table pedido_deliveries add column nfce_id integer default null;

            // alter table pedido_deliveries add column funcionario_id_agendamento integer default null;
            // alter table pedido_deliveries add column inicio_agendamento varchar(5) default null;
            // alter table pedido_deliveries add column fim_agendamento varchar(5) default null;
            // alter table pedido_deliveries add column data_agendamento date default null;
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedido_deliveries');
    }
};
