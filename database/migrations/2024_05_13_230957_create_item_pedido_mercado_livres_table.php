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
        Schema::create('item_pedido_mercado_livres', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pedido_id')->constrained('pedido_mercado_livres');
            $table->foreignId('produto_id')->nullable()->constrained('produtos');
            $table->string('item_id', 20);
            $table->string('item_nome', 100);
            $table->string('condicao', 20);
            $table->string('variacao_id', 20)->nullable();

            $table->decimal('quantidade', 8,2);
            $table->decimal('valor_unitario', 12,2);
            $table->decimal('sub_total', 12,2);
            $table->decimal('taxa_venda', 12,2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_pedido_mercado_livres');
    }
};
