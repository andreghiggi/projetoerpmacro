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
        Schema::create('fatura_nfces', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('nfce_id')->nullable()->constrained('nfces');
            $table->string('tipo_pagamento', 2); 
            $table->date('data_vencimento');
            $table->decimal('valor', 10,2);
            $table->string('observacao', 100)->nullable();

            // alter table fatura_nfces add column observacao varchar(100) default null;

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fatura_nfces');
    }
};
