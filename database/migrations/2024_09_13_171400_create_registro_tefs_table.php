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
        Schema::create('registro_tefs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('nfce_id')->nullable()->constrained('nfces');
            $table->string('nome_rede', 20);
            $table->string('nsu', 20);
            $table->string('data_transacao', 20);
            $table->string('hora_transacao', 20);
            $table->string('valor_total', 20);
            $table->string('hash', 20);
            $table->enum('estado', ['aprovado', 'cancelado', 'pendente']);
            $table->integer('usuario_id')->nullable();
            
            // alter table registro_tefs add column usuario_id integer default null;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registro_tefs');
    }
};
