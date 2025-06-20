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
        Schema::create('fatura_cotacaos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cotacao_id')->nullable()->constrained('cotacaos');
            $table->string('tipo_pagamento', 2)->nullable(); 
            $table->date('data_vencimento');
            $table->decimal('valor', 10,2);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fatura_cotacaos');
    }
};
