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
        Schema::create('financeiro_planos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->nullable()->constrained('empresas');
            $table->foreignId('plano_id')->nullable()->constrained('planos');
            $table->decimal('valor', 10, 2);
            $table->string('tipo_pagamento', 50);
            $table->enum('status_pagamento', ['pendente', 'recebido', 'cancelado']);
            $table->integer('plano_empresa_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financeiro_planos');
    }
};
