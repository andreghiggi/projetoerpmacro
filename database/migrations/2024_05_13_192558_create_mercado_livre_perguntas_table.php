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
        Schema::create('mercado_livre_perguntas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas');
            $table->string('_id', 20);
            $table->string('item_id', 20);
            $table->string('status', 20);
            $table->text('texto');
            $table->text('resposta');
            $table->timestamp('data');

            // alter table mercado_livre_perguntas add column resposta text;

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mercado_livre_perguntas');
    }
};
