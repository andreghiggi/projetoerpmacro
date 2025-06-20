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
        Schema::create('manifesto_dves', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->nullable()->constrained('empresas');

            $table->string('chave', 44);
            $table->string('nome', 100);
            $table->string('documento', 20);
            $table->decimal('valor', 10, 2);
            $table->string('num_prot', 20);
            $table->string('data_emissao', 25);
            $table->integer('sequencia_evento');
            $table->boolean('fatura_salva');
            $table->integer('tipo');
            $table->integer('nsu');
            $table->integer('compra_id')->default(0);
            $table->integer('nNf')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manifesto_dves');
    }
};
