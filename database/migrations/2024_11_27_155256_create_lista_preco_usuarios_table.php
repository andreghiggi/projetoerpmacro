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
        Schema::create('lista_preco_usuarios', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lista_preco_id')->constrained('lista_precos');
            $table->foreignId('usuario_id')->constrained('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lista_preco_usuarios');
    }
};
