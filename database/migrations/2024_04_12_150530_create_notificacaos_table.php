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
        Schema::create('notificacaos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->nullable()->constrained('empresas');
            $table->string('tabela', 60)->nullable();
            $table->text('descricao');
            $table->string('descricao_curta', 60);
            $table->string('titulo', 30);
            $table->integer('referencia')->nullable();
            $table->boolean('status')->default(1);
            $table->boolean('visualizada')->default(0);
            $table->boolean('por_sistema')->default(0);
            $table->enum('prioridade', ['baixa', 'media', 'alta']);
            $table->boolean('super')->default(0);

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificacaos');
    }
};
