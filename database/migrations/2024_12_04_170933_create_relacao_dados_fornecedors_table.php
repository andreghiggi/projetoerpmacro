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
        Schema::create('relacao_dados_fornecedors', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas');
            $table->string('cst_csosn_entrada', 3)->nullable();
            $table->string('cfop_entrada', 4)->nullable();
            $table->string('cst_csosn_saida', 3)->nullable();
            $table->string('cfop_saida', 4)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relacao_dados_fornecedors');
    }
};
