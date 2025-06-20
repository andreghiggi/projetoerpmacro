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
        Schema::create('hospede_reservas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('reserva_id')->constrained('reservas');
            $table->string('descricao', 20);
            $table->string('nome_completo', 100)->nullable();
            $table->string('cpf', 14)->nullable();
            $table->string('rua', 60)->nullable();
            $table->string('numero', 10)->nullable();
            $table->string('bairro', 10)->nullable();
            $table->foreignId('cidade_id')->nullable()->constrained('cidades');
            $table->string('telefone', 15)->nullable();
            $table->string('cep', 10)->nullable();
            $table->string('email', 60)->nullable();
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospede_reservas');
    }
};
