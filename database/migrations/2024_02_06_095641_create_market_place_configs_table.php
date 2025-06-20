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
        Schema::create('market_place_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->nullable()->constrained('empresas');
            $table->foreignId('cidade_id')->nullable()->constrained('cidades');

            $table->string('loja_id', 30)->nullable();
            $table->string('link_facebook')->nullable();
            $table->string('link_instagram')->nullable();
            $table->string('link_whatsapp')->nullable();
            $table->string('telefone', 20);
            $table->string('rua', 80);
            $table->string('numero', 15);
            $table->string('bairro', 30);
            $table->string('cep', 9);
            $table->string('email', 80);

            $table->string('tempo_medio_entrega', 10)->nullable();
            $table->decimal('valor_entrega', 10, 2)->nullable();
            $table->string('nome', 50);
            $table->string('descricao', 200)->nullable();
            $table->string('latitude', 15)->nullable();
            $table->string('longitude', 15)->nullable();
            $table->decimal('valor_entrega_gratis', 10, 2)->nullable();
            $table->boolean('usar_bairros')->default(1);
            $table->boolean('status')->default(0);
            $table->boolean('notificacao_novo_pedido')->default(1);

            $table->string('mercadopago_public_key', 120)->nullable();
            $table->string('mercadopago_access_token', 120)->nullable();

            $table->enum('tipo_divisao_pizza', ['divide', 'valor_maior'])->default('divide');
            $table->string('logo', 25);
            $table->string('fav_icon', 25);
            $table->string('tipos_pagamento', 255)->default('[]');
            $table->string('segmento', 100)->default('[]');

            $table->decimal('pedido_minimo', 10, 2)->nullable();
            $table->decimal('avaliacao_media', 10, 2);
            $table->string('api_token', 50)->nullable();
            $table->boolean('autenticacao_sms')->default(0);
            $table->boolean('confirmacao_pedido_cliente')->default(0);
            $table->string('tipo_entrega', 30);
            $table->string('cor_principal', 10)->nullable();
            $table->string('funcionamento_descricao', 100)->nullable();

            // alter table market_place_configs add column tipo_entrega varchar(30) default '';
            // alter table market_place_configs add column loja_id varchar(15) default null;
            // alter table market_place_configs add column cor_principal varchar(10) default null;
            // alter table market_place_configs add column email varchar(80) default null;
            // alter table market_place_configs add column funcionamento_descricao varchar(100) default null;

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_place_configs');
    }
};
