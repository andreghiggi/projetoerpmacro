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
        Schema::create('ordem_servicos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->nullable()->constrained('empresas');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes');
            $table->foreignId('usuario_id')->nullable()->constrained('users');

            $table->foreignId('funcionario_id')->nullable()->constrained('funcionarios');

            $table->string('estado', 2)->default("pd");
            $table->text('descricao');

            $table->string('forma_pagamento', 10)->default("av");
            $table->decimal('valor', 10, 2)->default(0);
            $table->timestamp('data_inicio');
            $table->timestamp('data_entrega')->nullable();
            $table->integer('nfe_id')->default(0);
            $table->integer('codigo_sequencial')->nullable();
            $table->foreignId('caixa_id')->nullable()->constrained('caixas');
            
            $table->integer('local_id')->nullable();
            
            $table->decimal('adiantamento', 10, 2)->default(0);

            // alter table ordem_servicos modify column descricao text;
            // alter table ordem_servicos add column codigo_sequencial integer default null;
            // alter table ordem_servicos add column caixa_id integer default null;

            // alter table ordem_servicos add column local_id integer default null;
            // alter table ordem_servicos add column status_entrega boolean default 0;
            // alter table ordem_servicos add column adiantamento decimal(10,2) default 0;
            

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordem_servicos');
    }
};
