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
        Schema::create('item_pedido_ecommerces', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pedido_id')->constrained('pedido_ecommerces');
            $table->foreignId('produto_id')->constrained('produtos');
            $table->foreignId('variacao_id')->nullable()->constrained('produto_variacaos');

            $table->decimal('quantidade', 8,3);
            $table->decimal('valor_unitario', 10,2);
            $table->decimal('sub_total', 10,2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_pedido_ecommerces');
    }
};
