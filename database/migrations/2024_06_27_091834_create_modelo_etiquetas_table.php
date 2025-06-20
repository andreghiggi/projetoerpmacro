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
        Schema::create('modelo_etiquetas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->nullable()->constrained('empresas');
            $table->string('nome', 40);
            $table->string('observacao', 255)->nullable();

            $table->decimal('altura', 7,2);
            $table->decimal('largura', 7,2);
            $table->integer('etiquestas_por_linha');
            $table->decimal('distancia_etiquetas_lateral', 7,2);
            $table->decimal('distancia_etiquetas_topo', 7,2);
            $table->integer('quantidade_etiquetas');

            $table->decimal('tamanho_fonte', 7,2);
            $table->decimal('tamanho_codigo_barras', 7,2);

            $table->boolean('nome_empresa');
            $table->boolean('nome_produto');
            $table->boolean('valor_produto');
            $table->boolean('codigo_produto');
            $table->boolean('codigo_barras_numerico');
            $table->boolean('importado_super')->default(0);
            $table->enum('tipo', ['simples', 'gondola']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modelo_etiquetas');
    }
};
