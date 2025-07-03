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
        Schema::table('produtos', function (Blueprint $table) {
            $table->boolean('solicita_observacao')->default(false);
            $table->integer('conecta_venda_multiplicador')->nullable();
            $table->integer('conecta_venda_qtd_minima')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->boolean('solicita_observacao')->default('false');
            $table->integer('conecta_venda_multiplicador')->nullable();
            $table->integer('conecta_venda_qtd_minima')->nullable();
        });
    }
};
