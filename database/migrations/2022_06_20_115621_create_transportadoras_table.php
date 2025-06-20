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
        Schema::create('transportadoras', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->nullable()->constrained('empresas');
            $table->string('razao_social', 60);
            $table->string('nome_fantasia', 60);
            $table->string('cpf_cnpj', 20);
            $table->string('ie', 20)->nullable();

            $table->string('email', 60);
            $table->string('telefone', 20);

            $table->foreignId('cidade_id')->nullable()->constrained('cidades');

            $table->string('rua', 60);
            $table->string('cep', 9);
            $table->string('numero', 10);
            $table->string('bairro', 40);
            $table->string('complemento', 60)->nullable();

            $table->string('antt', 20)->nullable();
            $table->integer('_id_import')->nullable();

            // alter table transportadoras add column _id_import integer default null;
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transportadoras');
    }
};
