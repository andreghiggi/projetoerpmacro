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
        Schema::create('fatura_pre_vendas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pre_venda_id')->constrained('pre_vendas')->onDelete('cascade');

            $table->decimal('valor_parcela', 16, 7);
            $table->string('tipo_pagamento', 20);
            $table->date('vencimento');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fatura_pre_vendas');
    }
};
