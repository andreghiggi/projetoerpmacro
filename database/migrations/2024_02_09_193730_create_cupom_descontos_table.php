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
        Schema::create('cupom_descontos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes');
            $table->enum('tipo_desconto', ['valor', 'percentual']);
            $table->string('codigo', 6);
            $table->string('descricao', 50)->nullable();
            $table->decimal('valor', 10, 4);
            $table->decimal('valor_minimo_pedido', 12, 4);
            $table->boolean('status')->default(1);
            $table->date('expiracao')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cupom_descontos');
    }
};
