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
        Schema::create('nuvem_shop_item_pedidos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('produto_id')->nullable()->constrained('produtos');
            $table->foreignId('pedido_id')->nullable()->constrained('nuvem_shop_pedidos');

            $table->decimal('quantidade', 8, 2);
            $table->decimal('valor_unitario', 10, 2);
            $table->decimal('sub_total', 10, 2);
            $table->string('nome', 100);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nuvem_shop_item_pedidos');
    }
};
