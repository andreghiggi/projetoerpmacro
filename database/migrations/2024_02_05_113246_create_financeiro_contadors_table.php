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
        Schema::create('financeiro_contadors', function (Blueprint $table) {
            $table->id();

            $table->foreignId('contador_id')->nullable()->constrained('empresas');
            $table->decimal('percentual_comissao', 5,2);
            $table->decimal('valor_comissao', 10, 2);
            $table->decimal('total_venda', 10, 2);
            $table->string('mes', 20);
            $table->integer('ano');
            $table->string('tipo_pagamento', 30)->nullable();
            $table->string('observacao', 100)->nullable();
            $table->boolean('status_pagamento')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financeiro_contadors');
    }
};
