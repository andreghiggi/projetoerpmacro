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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            $table->string('imagem', 25)->nullable();
            $table->boolean('admin')->default(1);
            $table->boolean('sidebar_active')->default(1);
            $table->boolean('notificacao_cardapio')->default(0);
            $table->boolean('notificacao_marketplace')->default(0);
            $table->boolean('notificacao_ecommerce')->default(0);
            $table->boolean('tipo_contador')->default(0);
            $table->boolean('escolher_localidade_venda')->default(0);
            $table->boolean('suporte')->default(0);
            $table->boolean('status')->default(1);

            $table->rememberToken();
            $table->timestamps();

            // alter table users add column imagem varchar(25) default '';
            // alter table users add column admin boolean default 1;
            // alter table users add column sidebar_active boolean default 1;
            // alter table users add column notificacao_cardapio boolean default 0;
            // alter table users add column notificacao_marketplace boolean default 0;
            // alter table users add column notificacao_ecommerce boolean default 0;
            // alter table users add column tipo_contador boolean default 0;
            // alter table users add column escolher_localidade_venda boolean default 0;
            // alter table users add column suporte boolean default 0;
            // alter table users add column status boolean default 1;

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
