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
        Schema::create('crm_anotacaos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes');
            $table->foreignId('fornecedor_id')->nullable()->constrained('fornecedors');
            $table->integer('funcionario_id')->nullable();

            $table->integer('registro_id')->nullable();
            $table->string('tipo_registro')->nullable();

            $table->enum('status', ['positivo', 'bom', 'negativo'])->nullable();
            $table->string('conclusao', 100)->nullable();
            $table->string('assunto');

            $table->boolean('alerta');
            $table->date('data_retorno')->nullable();
            $table->date('data_entrega')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_anotacaos');
    }
};
