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
        Schema::table('conecta_venda_pedidos', function (Blueprint $table) {
            $table->string('cupom', 100)->after('frete_tipo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conecta_venda_pedidos', function (Blueprint $table) {
            $table->string('cupom', 100)->after('frete_tipo')->nullable();
        });
    }
};
