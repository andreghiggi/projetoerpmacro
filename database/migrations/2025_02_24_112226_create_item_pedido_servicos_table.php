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
        Schema::create('item_pedido_servicos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pedido_id')->constrained('pedidos');
            $table->foreignId('servico_id')->constrained('servicos');

            $table->string('observacao', 255)->nullable();
            $table->enum('estado', ['novo', 'pendente', 'preparando', 'finalizado'])->default('novo');

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
        Schema::dropIfExists('item_pedido_servicos');
    }
};
