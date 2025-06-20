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
        Schema::create('laboratorios', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas');
            $table->string('nome', 60);
            $table->string('cnpj', 14)->nullable();
            $table->string('email', 60)->nullable();
            $table->string('telefone', 20)->nullable();
            
            $table->foreignId('cidade_id')->nullable()->constrained('cidades');
            $table->string('rua', 60)->nullable();
            $table->string('cep', 9)->nullable();
            $table->string('numero', 10)->nullable();
            $table->string('bairro', 40)->nullable();
            $table->boolean('status');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laboratorios');
    }
};
