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
        Schema::create('conecta_venda_config', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas');

            $table->string('client_id', 10)->default('');
            $table->string('client_secret', 150)->default('');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conecta_venda_config');
    }
};
