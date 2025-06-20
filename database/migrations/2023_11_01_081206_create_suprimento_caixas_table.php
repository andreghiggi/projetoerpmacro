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
        Schema::create('suprimento_caixas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('caixa_id')->nullable()->constrained('caixas');
            $table->decimal('valor', 10, 2);
            $table->string('observacao', 200);
            $table->integer('conta_empresa_id')->nullable();
            $table->string('tipo_pagamento', 2)->nullable();
            
            // alter table suprimento_caixas add column conta_empresa_id integer default null;
            // alter table suprimento_caixas add column tipo_pagamento varchar(2) default null;
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suprimento_caixas');
    }
};
