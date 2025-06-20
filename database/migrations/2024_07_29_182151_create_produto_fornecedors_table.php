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
        Schema::create('produto_fornecedors', function (Blueprint $table) {
            $table->id();

            $table->foreignId('produto_id')->nullable()->constrained('produtos');
            $table->foreignId('fornecedor_id')->nullable()->constrained('fornecedors');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produto_fornecedors');
    }
};
