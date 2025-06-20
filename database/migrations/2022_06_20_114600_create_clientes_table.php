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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->nullable()->constrained('empresas');

            $table->string('razao_social', 60);
            $table->string('nome_fantasia', 60)->nullable();
            $table->string('cpf_cnpj', 20)->nullable();
            $table->string('ie', 20)->nullable();
            $table->integer('numero_sequencial')->nullable();
            
            $table->boolean('contribuinte')->default(0);
            $table->boolean('consumidor_final')->default(0);
            $table->string('email', 60)->nullable();
            $table->string('telefone', 20)->nullable();

            $table->foreignId('cidade_id')->nullable()->constrained('cidades');

            $table->string('rua', 60);
            $table->string('cep', 9);
            $table->string('numero', 10);
            $table->string('bairro', 40);
            $table->string('complemento', 60)->nullable();
            $table->boolean('status')->default(1);
            $table->integer('token')->nullable();
            $table->string('uid', 30)->nullable();
            $table->string('senha', 200)->nullable();
            $table->decimal('valor_cashback', 10,2)->default(0);
            $table->decimal('valor_credito', 10,2)->default(0);
            $table->string('nuvem_shop_id', 20)->nullable();
            $table->decimal('limite_credito', 10,2)->nullable();

            $table->integer('lista_preco_id')->nullable();
            $table->integer('_id_import')->nullable();

            $table->string('codigo_pais', 4)->nullable();
            $table->string('id_estrangeiro', 30)->nullable();

            // alter table clientes add column status boolean default 1;
            // alter table clientes add column uid varchar(30) default null;
            // alter table clientes modify column senha varchar(200) default null;
            // alter table clientes modify column nome_fantasia varchar(60) default null;
            // alter table clientes modify column cpf_cnpj varchar(20) default null;
            // alter table clientes add column token integer default null;
            // alter table clientes add column valor_cashback decimal(10,2) default 0;
            // alter table clientes add column valor_credito decimal(10,2) default 0;
            // alter table clientes add column nuvem_shop_id varchar(20) default null;
            // alter table clientes add column limite_credito decimal(10,2) default null;

            // alter table clientes add column lista_preco_id integer default null;
            // alter table clientes add column _id_import integer default null;

            // alter table clientes add column id_estrangeiro varchar(30) default null;
            // alter table clientes add column codigo_pais varchar(4) default null;
            // alter table clientes add column numero_sequencial integer default null;

            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
