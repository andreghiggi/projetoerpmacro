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
        Schema::create('escritorio_contabils', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('cidade_id')->nullable()->constrained('cidades');

            $table->string('razao_social', 100);
            $table->string('nome_fantasia', 80);
            $table->string('cnpj', 19)->nullable();
            $table->string('ie', 20)->nullable();
            $table->string('email', 80)->nullable();

            $table->string('rua', 80);
            $table->string('numero', 10);
            $table->string('bairro', 50);
            $table->string('telefone', 20);
            $table->string('cep', 10);
            $table->string('crc', 20)->nullable();
            $table->string('cpf', 15)->nullable();
            $table->string('token_sieg', 255)->nullable();

            $table->boolean('envio_xml_automatico')->default(0);

            // alter table escritorio_contabils add column token_sieg varchar(255) default null;

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escritorio_contabils');
    }
};
