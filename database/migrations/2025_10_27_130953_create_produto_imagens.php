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
        Schema::create('produto_imagens', function (Blueprint $table) {
            $table->id();
            $table->integer('produto_id')->default(0);
            $table->integer('produto_variacao_id')->default(0);
            $table->string('imagem', 25)->nullable();
            $table->integer('ordem')->default(0);
            $table->char('sha256', 32)-> charset('binary')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produto_imagens');
    }
};
