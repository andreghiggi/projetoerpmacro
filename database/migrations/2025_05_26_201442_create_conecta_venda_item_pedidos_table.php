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
        Schema::create('conecta_venda_item_pedidos', function (Blueprint $table) {
            $table->id();
            $table->string('pedido_id');
            $table->string('produto_id'); // ID vindo da API Conecta Venda
            $table->string('variacao_id')->nullable();
            $table->string('nome')->nullable();
            $table->string('referencia')->nullable();
            $table->string('descricao')->nullable();
            $table->string('ean')->nullable();
            $table->decimal('peso', 10, 3)->nullable();
            $table->integer('quantidade')->default(0);
            $table->decimal('valor_unitario', 10, 2)->default(0.00);
            $table->text('observacao')->nullable();
            $table->decimal('sub_total', 10, 2)->default(0.00);

            $table->foreign('pedido_id')->references('id')->on('conecta_venda_pedidos')->onDelete('cascade');

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conecta_venda_item_pedidos');
    }
};
