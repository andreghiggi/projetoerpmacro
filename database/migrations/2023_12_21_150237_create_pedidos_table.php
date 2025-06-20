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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes');
            $table->foreignId('funcionario_id')->nullable()->constrained('funcionarios');

            $table->string('cliente_nome', 100)->nullable();
            $table->string('cliente_fone', 20)->nullable();
            $table->string('comanda', 10);
            $table->string('observacao', 255)->nullable();
            $table->string('tipo_pagamento', 2)->nullable();
            $table->string('mesa', 10)->nullable();

            $table->timestamp('data_fechamento')->nullable();
            $table->decimal('total', 12, 2);
            $table->boolean('status')->default(1);
            $table->boolean('em_atendimento')->default(1);
            $table->integer('nfce_id')->nullable();

            // alter table pedidos add column em_atendimento boolean default 1;
            // alter table pedidos add column nfce_id integer default null;
            // alter table pedidos add column funcionario_id integer default null;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
