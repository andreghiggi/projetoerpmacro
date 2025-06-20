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
        Schema::create('lacre_unidade_cargas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('info_id')->constrained('info_descargas')->onDelete('cascade');
            $table->string('numero', 20);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lacre_unidade_cargas');
    }
};
