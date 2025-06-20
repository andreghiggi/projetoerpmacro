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
        Schema::create('item_pre_vendas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pre_venda_id')->constrained('pre_vendas')->onDelete('cascade');

            $table->foreignId('produto_id')->constrained('produtos');
            $table->foreignId('variacao_id')->nullable()->constrained('produto_variacaos');

            $table->decimal('quantidade', 10,3);
            $table->decimal('valor', 16, 7);
            $table->string('observacao', 80);
            $table->integer('cfop')->default(0);

            // alter table item_pre_vendas add column variacao_id integer default null;

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_pre_vendas');
    }
};
