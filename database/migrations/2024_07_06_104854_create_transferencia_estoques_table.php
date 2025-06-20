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
        Schema::create('transferencia_estoques', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->nullable()->constrained('empresas');
            $table->foreignId('local_saida_id')->nullable()->constrained('localizacaos');
            $table->foreignId('local_entrada_id')->nullable()->constrained('localizacaos');
            $table->foreignId('usuario_id')->nullable()->constrained('users');

            $table->string('observacao', 255)->nullable();
            $table->string('codigo_transacao', 10);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transferencia_estoques');
    }
};
