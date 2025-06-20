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
        Schema::create('item_lista_precos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lista_id')->nullable()->constrained('lista_precos');
            $table->foreignId('produto_id')->nullable()->constrained('produtos');
            $table->decimal('valor', 10, 2);
            $table->decimal('percentual_lucro', 10, 2);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_lista_precos');
    }
};
