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
        Schema::create('planos', function (Blueprint $table) {
            $table->id();

            $table->string('nome', 40);
            $table->text('descricao');

            $table->integer('maximo_nfes');
            $table->integer('maximo_nfces');
            $table->integer('maximo_ctes');
            $table->integer('maximo_mdfes');
            $table->integer('maximo_usuarios');
            $table->integer('maximo_locais');
            $table->string('imagem', 25);
            $table->boolean('visivel_clientes')->default(1);
            $table->boolean('visivel_contadores')->default(0);
            $table->boolean('status')->default(1);

            $table->decimal('valor', 10,2);
            $table->decimal('valor_implantacao', 10,2)->default(0);
            $table->integer('intervalo_dias');
            $table->text('modulos');
            $table->boolean('auto_cadastro');
            $table->boolean('fiscal');
            $table->integer('segmento_id')->nullable();
            
            $table->timestamps();

           // alter table planos add column maximo_mdfes integer default null;
           // alter table planos add column modulos text;
           // alter table planos add column visivel_contadores boolean default 0;
           // alter table planos add column valor_implantacao decimal(10,2) default 0;
           // alter table planos add column auto_cadastro boolean default 0;
           // alter table planos add column fiscal boolean default 1;

           // alter table planos add column segmento_id integer default null;
           // alter table planos add column maximo_usuarios integer default null;
           // alter table planos add column maximo_locais integer default null;


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planos');
    }
};
