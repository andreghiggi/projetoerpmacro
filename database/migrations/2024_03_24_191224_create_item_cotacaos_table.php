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
        Schema::create('item_cotacaos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cotacao_id')->nullable()->constrained('cotacaos');
            $table->foreignId('produto_id')->nullable()->constrained('produtos');

            $table->decimal('valor_unitario', 12,3)->nullable();
            $table->decimal('quantidade', 12,3);
            $table->decimal('sub_total', 12,3)->nullable();
            $table->string('observacao', 120)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_cotacaos');
    }
};
