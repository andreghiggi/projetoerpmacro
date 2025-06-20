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
        Schema::create('inutilizacaos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->nullable()->constrained('empresas');

            $table->integer('numero_inicial');
            $table->integer('numero_final');
            $table->string('numero_serie', 3);
            $table->enum('modelo', ['55', '65']);
            $table->string('justificativa', 200);
            $table->enum('estado', ['novo', 'aprovado', 'rejeitado']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inutilizacaos');
    }
};
