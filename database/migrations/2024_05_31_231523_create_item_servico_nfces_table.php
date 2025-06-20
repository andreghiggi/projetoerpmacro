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
        Schema::create('item_servico_nfces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nfce_id')->constrained('nfces');
            $table->foreignId('servico_id')->constrained('servicos');
            $table->decimal('quantidade', 6,2);
            $table->decimal('valor_unitario', 10,2);
            $table->decimal('sub_total', 10,2);
            $table->string('observacao', 50)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_servico_nfces');
    }
};
