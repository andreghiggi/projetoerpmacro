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
        Schema::create('item_pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos');
            $table->foreignId('produto_id')->constrained('produtos');
            $table->foreignId('funcionario_id')->nullable()->constrained('funcionarios');

            $table->string('observacao', 255)->nullable();
            $table->enum('estado', ['novo', 'pendente', 'preparando', 'finalizado'])->default('novo');

            $table->decimal('quantidade', 8,3);
            $table->decimal('valor_unitario', 10,2);
            $table->decimal('sub_total', 10,2);
            $table->integer('tempo_preparo')->nullable();
            $table->string('ponto_carne', 30)->nullable();

            $table->foreignId('tamanho_id')->nullable()->constrained('tamanho_pizzas');

            // alter table produtos add column tempo_preparo integer default null;
            // alter table produtos add column ponto_carne varchar(30) default null;
            // alter table item_pedidos add column tamanho_id integer default null;
            // alter table item_pedidos add column funcionario_id integer default null;
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_pedidos');
    }
};
