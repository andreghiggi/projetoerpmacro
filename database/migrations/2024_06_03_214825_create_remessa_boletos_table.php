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
        Schema::create('remessa_boletos', function (Blueprint $table) {
            $table->id();

            $table->string('nome_arquivo', 40);
            $table->integer('conta_boleto_id');
            $table->foreignId('empresa_id')->nullable()->constrained('empresas');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remessa_boletos');
    }
};
