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
        Schema::create('acomodacaos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->string('nome', 50);
            $table->string('numero', 15);
            $table->foreignId('categoria_id')->constrained('categoria_acomodacaos')->onDelete('cascade');;

            $table->decimal('valor_diaria', 12, 2);
            $table->integer('capacidade');
            $table->string('estacionamento', 15)->nullable();
            $table->text('descricao');
            $table->boolean('status');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acomodacaos');
    }
};
