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
        Schema::create('fatura_reservas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('reserva_id')->nullable()->constrained('reservas');
            $table->string('tipo_pagamento', 2); 
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
        Schema::dropIfExists('fatura_reservas');
    }
};
