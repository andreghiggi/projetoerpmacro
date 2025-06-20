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
        Schema::create('medida_ctes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cte_id')->constrained('ctes');

            $table->string('cod_unidade', 2);
            $table->string('tipo_medida', 20);
            $table->decimal('quantidade', 10, 4);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medida_ctes');
    }
};
