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
        Schema::create('item_ordem_producaos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ordem_producao_id')->constrained('ordem_producaos');
            $table->foreignId('item_producao_id')->constrained('item_producaos');
            $table->foreignId('produto_id')->constrained('produtos');

            $table->decimal('quantidade', 12,3);
            $table->boolean('status')->default(0);
            $table->string('observacao', 100)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_ordem_producaos');
    }
};
