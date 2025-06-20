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
        Schema::create('cupom_desconto_clientes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('cupom_id')->constrained('cupom_descontos');
            $table->foreignId('pedido_id')->constrained('pedido_deliveries');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cupom_desconto_clientes');
    }
};
