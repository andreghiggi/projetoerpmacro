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
        Schema::create('categoria_produtos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas');
            $table->string('nome', 60);
            $table->string('nome_en', 60)->nullable();
            $table->string('nome_es', 60)->nullable();
            $table->boolean('status')->default(1);
            $table->boolean('cardapio')->default(0);
            $table->boolean('delivery')->default(0);
            $table->boolean('ecommerce')->default(0);
            $table->boolean('reserva')->default(0);
            $table->boolean('tipo_pizza')->default(0);
            $table->string('hash_ecommerce', 50)->nullable();
            $table->string('hash_delivery', 50)->nullable();

            $table->foreignId('categoria_id')->nullable()->constrained('categoria_produtos');
            $table->integer('_id_import')->nullable();


            // alter table categoria_produtos add column status boolean default 1;

            // alter table categoria_produtos add column tipo_pizza boolean default 0;
            // alter table categoria_produtos add column delivery boolean default 0;
            // alter table categoria_produtos add column ecommerce boolean default 0;
            // alter table categoria_produtos add column reserva boolean default 0;
            // alter table categoria_produtos add column hash_ecommerce varchar(50) default null;
            // alter table categoria_produtos add column hash_delivery varchar(50) default null;

            // alter table categoria_produtos add column categoria_id integer default null;
            // alter table categoria_produtos add column _id_import integer default null;

            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categoria_produtos');
    }
};
