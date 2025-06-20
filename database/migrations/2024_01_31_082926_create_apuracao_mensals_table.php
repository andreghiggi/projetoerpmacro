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
        Schema::create('apuracao_mensals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('funcionario_id')->constrained('funcionarios')->onDelete('cascade');
            $table->string('mes', 20);
            $table->integer('ano');
            $table->decimal('valor_final', 10, 2);
            $table->string('forma_pagamento', 30);
            $table->string('observacao', 100)->nullable();
            $table->integer('conta_pagar_id')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apuracao_mensals');
    }
};
