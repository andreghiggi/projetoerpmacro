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
        Schema::create('cash_back_clientes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('cliente_id')->constrained('clientes');

            $table->enum('tipo', ['venda', 'pdv']);
            $table->integer('venda_id');
            $table->decimal('valor_venda', 16, 7);
            $table->decimal('valor_credito', 16, 7);
            $table->decimal('valor_percentual', 5, 2);
            $table->date('data_expiracao');
            $table->boolean('status')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_back_clientes');
    }
};
