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
        Schema::create('remessa_boleto_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('remessa_id')->nullable()->constrained('remessa_boletos');
            $table->foreignId('boleto_id')->nullable()->constrained('boletos');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remessa_boleto_items');
    }
};
