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
        Schema::create('movimentacao_produtos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('produto_id')->nullable()->constrained('produtos');
            $table->decimal('quantidade', 14, 4);
            $table->enum('tipo', ['incremento', 'reducao']);
            $table->integer('codigo_transacao');
            $table->integer('user_id')->nullable();
            $table->enum('tipo_transacao', ['venda_nfe', 'venda_nfce', 'compra', 'alteracao_estoque']);
            $table->foreignId('produto_variacao_id')->nullable()->constrained('produto_variacaos');
            $table->decimal('estoque_atual', 14, 4);

            // alter table movimentacao_produtos add column produto_variacao_id integer default null;
            // alter table movimentacao_produtos add column user_id integer default null;
            // alter table movimentacao_produtos add column estoque_atual decimal(14,4) default 0;
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimentacao_produtos');
    }
};
