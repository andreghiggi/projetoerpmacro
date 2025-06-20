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
        Schema::create('estoques', function (Blueprint $table) {
            $table->id();

            $table->foreignId('produto_id')->nullable()->constrained('produtos');
            $table->foreignId('produto_variacao_id')->nullable()->constrained('produto_variacaos');
            $table->decimal('quantidade', 14, 4);
            $table->integer('local_id')->nullable();

            // alter table estoques add column produto_variacao_id integer default null;
            // alter table estoques add column local_id integer default null;
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estoques');
    }
};
