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
        Schema::create('funcionamentos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('funcionario_id')->nullable()->constrained('funcionarios');
            $table->time('inicio');
            $table->time('fim');
            $table->enum('dia_id', ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo']);

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funcionamentos');
    }
};
