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
        Schema::create('woocommerce_item_pedidos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pedido_id')->constrained('woocommerce_pedidos');
            $table->foreignId('produto_id')->nullable()->constrained('produtos');
            $table->string('item_id', 20);
            $table->string('item_nome', 100);
            $table->decimal('quantidade', 8,2);
            $table->decimal('valor_unitario', 12,2);
            $table->decimal('sub_total', 12,2);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('woocommerce_item_pedidos');
    }
};
