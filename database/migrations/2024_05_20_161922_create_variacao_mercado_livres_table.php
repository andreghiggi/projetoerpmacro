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
        Schema::create('variacao_mercado_livres', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas');
            
            $table->foreignId('produto_id')->nullable()->constrained('produtos');
            $table->string('_id', 20);
            $table->decimal('quantidade', 10, 2);
            $table->decimal('valor', 12, 2);
            $table->string('nome', 50);
            $table->string('valor_nome', 50);

            // alter table variacao_mercado_livres add column nome varchar(50);
            // alter table variacao_mercado_livres add column valor_nome varchar(50);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variacao_mercado_livres');
    }
};
