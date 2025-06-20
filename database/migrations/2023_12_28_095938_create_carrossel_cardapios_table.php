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
        Schema::create('carrossel_cardapios', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('produto_id')->nullable()->constrained('produtos');
            $table->string('descricao', 255)->nullable();
            $table->string('descricao_en', 255)->nullable();
            $table->string('descricao_es', 255)->nullable();
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
        Schema::dropIfExists('carrossel_cardapios');
    }
};
