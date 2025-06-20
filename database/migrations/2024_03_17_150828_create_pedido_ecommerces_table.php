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
        Schema::create('pedido_ecommerces', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('endereco_id')->nullable()->constrained('endereco_ecommerces');

            $table->enum('estado', ['novo', 'preparando', 'em_trasporte', 'finalizado', 'recusado']);
            $table->enum('tipo_pagamento', ['cartao', 'pix', 'boleto', 'deposito']);
            $table->decimal('valor_total', 10, 2);
            $table->decimal('valor_frete', 10, 2)->nullable();
            $table->decimal('desconto', 10, 2)->nullable();

            $table->string('tipo_frete', 20)->nullable();

            $table->string('rua_entrega', 50)->nullable();
            $table->string('numero_entrega', 10)->nullable();
            $table->string('referencia_entrega', 50)->nullable();
            $table->string('bairro_entrega', 30)->nullable();
            $table->string('cep_entrega', 10)->nullable();
            $table->string('cidade_entrega', 60)->nullable();

            $table->text('link_boleto');
            $table->text('qr_code_base64');
            $table->text('qr_code');
            $table->string('observacao', 100)->nullable();
            $table->string('hash_pedido', 30);
            $table->string('status_pagamento', 15)->default('');
            $table->string('transacao_id', 100)->default('');

            $table->integer('nfe_id')->nullable();
            $table->string('cupom_desconto', 6)->nullable();
            $table->string('data_entrega', 10)->nullable();
            $table->string('codigo_rastreamento', 20)->nullable();
            $table->boolean('pedido_lido')->default(0);

            $table->string('nome', 40)->nullable();
            $table->string('sobre_nome', 40)->nullable();
            $table->string('email', 60)->nullable();
            $table->enum('tipo_documento', ['cpf', 'cnpj'])->nullable();
            $table->string('numero_documento', 20)->nullable();
            $table->string('comprovante', 25)->nullable();

            // alter table pedido_ecommerces add column pedido_lido boolean default 0;
            // alter table pedido_ecommerces modify column tipo_pagamento enum('cartao', 'pix', 'boleto', 'deposito');
            // alter table pedido_ecommerces add column comprovante varchar(25) default null;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedido_ecommerces');
    }
};
