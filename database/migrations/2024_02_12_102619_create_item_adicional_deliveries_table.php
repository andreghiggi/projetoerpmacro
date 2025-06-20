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
        Schema::create('item_adicional_deliveries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('item_pedido_id')->constrained('item_pedido_deliveries');
            $table->foreignId('adicional_id')->constrained('adicionals');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_adicional_deliveries');
    }
};
