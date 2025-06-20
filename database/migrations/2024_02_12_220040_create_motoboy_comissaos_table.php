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
        Schema::create('motoboy_comissaos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('pedido_id')->constrained('pedido_deliveries');
            $table->foreignId('motoboy_id')->constrained('motoboys');
            $table->decimal('valor', 10, 2);
            $table->decimal('valor_total_pedido', 10, 2);
            $table->boolean('status')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motoboy_comissaos');
    }
};
