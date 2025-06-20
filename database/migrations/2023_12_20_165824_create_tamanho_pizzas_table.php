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
        Schema::create('tamanho_pizzas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->string('nome', 50);
            $table->string('nome_en', 50)->nullable();
            $table->string('nome_es', 50)->nullable();
            $table->integer('maximo_sabores');
            $table->integer('quantidade_pedacos');
            $table->boolean('status')->default(1);

            // alter table tamanho_pizzas add column nome_en varchar(50) default null;
            // alter table tamanho_pizzas add column nome_es varchar(50) default null;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tamanho_pizzas');
    }
};
