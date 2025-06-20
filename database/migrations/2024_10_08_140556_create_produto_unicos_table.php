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
        Schema::create('produto_unicos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('nfe_id')->nullable()->constrained('nves');
            $table->foreignId('nfce_id')->nullable()->constrained('nfces');

            $table->foreignId('produto_id')->constrained('produtos');
            $table->string('codigo', 60);
            $table->string('observacao', 250)->nullable();
            $table->enum('tipo', ['entrada', 'saida']);
            $table->boolean('em_estoque');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produto_unicos');
    }
};
