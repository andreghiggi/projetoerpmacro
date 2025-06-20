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
        Schema::create('comissao_vendas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->nullable()->constrained('empresas');
            $table->foreignId('funcionario_id')->nullable()->constrained('funcionarios');

            $table->integer('nfe_id')->nullable();
            $table->integer('nfce_id')->nullable();

            $table->string('tabela', 14);
            $table->decimal('valor', 10, 2);
            $table->decimal('valor_venda', 10, 2);
            $table->boolean('status')->default(0);

            // alter table comissao_vendas add column valor_venda decimal(10,2) default 0;

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comissao_vendas');
    }
};
