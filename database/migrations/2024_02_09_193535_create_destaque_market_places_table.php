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
        Schema::create('destaque_market_places', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('produto_id')->nullable()->constrained('produtos');
            $table->foreignId('servico_id')->nullable()->constrained('servicos');
            $table->string('descricao', 255)->nullable();
            $table->decimal('valor', 12, 4)->nullable();
            $table->boolean('status')->default(1);
            $table->string('imagem', 25)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('destaque_market_places');
    }
};
