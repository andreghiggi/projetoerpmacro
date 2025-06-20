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
        Schema::create('reserva_configs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->nullable()->constrained('empresas');
            $table->string('cpf_cnpj', 18);
            $table->string('razao_social', 80);
            $table->string('rua', 80);
            $table->string('numero', 10);
            $table->string('bairro', 30);
            $table->string('cep', 10);
            $table->string('complemento', 200)->nullable();
            $table->foreignId('cidade_id')->constrained('cidades');

            $table->string('telefone', 15);

            $table->string('horario_checkin', 5);
            $table->string('horario_checkout', 5);

            $table->string('email', 60)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reserva_configs');
    }
};
