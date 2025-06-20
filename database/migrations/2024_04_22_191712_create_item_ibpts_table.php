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
        Schema::create('item_ibpts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ibpt_id')->nullable()->constrained('ibpts');
            $table->string('codigo', 8);
            $table->string('descricao', 80);
            $table->decimal('nacional_federal', 5,2);
            $table->decimal('importado_federal', 5,2);
            $table->decimal('estadual', 5,2);
            $table->decimal('municipal', 5,2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_ibpts');
    }
};
