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
        Schema::create('ecommerce_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->nullable()->constrained('empresas');

            $table->string('nome', 50);
            $table->string('loja_id', 30);
            $table->string('logo', 30);
            $table->string('descricao_breve', 200)->nullable();

            $table->string('rua', 80);
            $table->string('numero', 10);
            $table->string('bairro', 30);
            $table->string('cep', 10);
            $table->foreignId('cidade_id')->constrained('cidades');

            $table->string('telefone', 15);
            $table->string('email', 60)->nullable();
            $table->string('link_facebook', 120)->nullable();
            $table->string('link_whatsapp', 120)->nullable();
            $table->string('link_instagram', 120)->nullable();

            $table->decimal('frete_gratis_valor', 10, 2)->nullable();
            $table->string('mercadopago_public_key', 120);
            $table->string('mercadopago_access_token', 120);

            $table->boolean('habilitar_retirada')->default(false);
            $table->boolean('notificacao_novo_pedido')->default(1);

            $table->decimal('desconto_padrao_boleto', 4, 2)->nullable();
            $table->decimal('desconto_padrao_pix', 4, 2)->nullable();
            $table->decimal('desconto_padrao_cartao', 4, 2)->nullable();
            $table->string('tipos_pagamento', 255)->default('[]');
            $table->boolean('status')->default(1);
            $table->text('politica_privacidade');
            $table->text('termos_condicoes');
            $table->text('dados_deposito');
            $table->string('cor_principal', 10)->nullable();

            // alter table ecommerce_configs add column termos_condicoes text;
            // alter table ecommerce_configs add column dados_deposito text;

            // alter table ecommerce_configs add column cor_principal varchar(10) default '#000';

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ecommerce_configs');
    }
};
