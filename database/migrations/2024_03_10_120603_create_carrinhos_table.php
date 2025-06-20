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
        Schema::create('carrinhos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes');
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('endereco_id')->nullable()->constrained('endereco_ecommerces');

            $table->enum('estado', ['pendente', 'finalizado']);
            $table->decimal('valor_total', 10, 2);
            $table->string('tipo_frete', 20)->nullable();
            $table->decimal('valor_frete', 10, 2);
            $table->decimal('cep', 9)->nullable();
            $table->string('session_cart', 30);

            // alter table carrinhos add column tipo_frete varchar(20) default null;
            // alter table carrinhos add column cep varchar(9) default null;
            // alter table carrinhos modify column cep varchar(9) default null;

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carrinhos');
    }
};
