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
        Schema::create('bairro_delivery_masters', function (Blueprint $table) {
            $table->id();
            
            $table->string('nome', 60);
            $table->foreignId('cidade_id')->nullable()->constrained('cidades');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bairro_delivery_masters');
    }
};
