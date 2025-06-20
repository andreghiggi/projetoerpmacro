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
        Schema::create('componente_ctes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cte_id')->constrained('ctes');
            $table->string('nome', 30);
            $table->decimal('valor', 10, 4);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('componente_ctes');
    }
};
