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
        Schema::create('sped_configs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas');

            $table->string('codigo_conta_analitica', 30)->nullable();
            $table->string('codigo_receita', 30)->nullable();
            $table->boolean('gerar_bloco_k')->default(0);
            $table->integer('layout_bloco_k')->default(0);

            $table->string('codigo_obrigacao', 3)->default('000');
            $table->string('data_vencimento', 2)->default('10');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sped_configs');
    }
};
