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
        Schema::create('manutencao_veiculo_produtos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('manutencao_id')->constrained('manutencao_veiculos');
            $table->foreignId('produto_id')->nullable()->constrained('produtos');
            
            $table->decimal('quantidade', 6,2);
            $table->decimal('valor_unitario', 10,2);
            $table->decimal('sub_total', 10,2);
            $table->string('observacao', 200)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manutencao_veiculo_produtos');
    }
};
