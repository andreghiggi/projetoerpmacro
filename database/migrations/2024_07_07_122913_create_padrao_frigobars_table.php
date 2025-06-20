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
        Schema::create('padrao_frigobars', function (Blueprint $table) {
            $table->id();

            $table->foreignId('frigobar_id')->constrained('frigobars');
            $table->foreignId('produto_id')->constrained('produtos');
            $table->decimal('quantidade', 8, 2);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('padrao_frigobars');
    }
};
