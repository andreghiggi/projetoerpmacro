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
        Schema::create('manutencao_veiculos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('veiculo_id')->constrained('veiculos');
            $table->foreignId('fornecedor_id')->constrained('fornecedors');
            $table->integer('numero_sequencial')->nullable();
            
            $table->string('observacao', 200)->nullable();
            $table->decimal('total', 12,2);
            $table->decimal('desconto', 10,2)->nullable();
            $table->decimal('acrescimo', 10,2)->nullable();
            $table->boolean('conta_pagar_id')->nullable();
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();
            $table->enum('estado', ['aguardando', 'em_manutencao', 'finalizado']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manutencao_veiculos');
    }
};
