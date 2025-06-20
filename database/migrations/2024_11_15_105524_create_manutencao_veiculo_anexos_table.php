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
        Schema::create('manutencao_veiculo_anexos', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('manutencao_id')->constrained('manutencao_veiculos');
            $table->string('arquivo', 25)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manutencao_veiculo_anexos');
    }
};
