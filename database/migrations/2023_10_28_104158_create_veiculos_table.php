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
        Schema::create('veiculos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas');
            $table->string('placa', 8);
            $table->string('uf', 2);
            $table->string('cor', 10);
            $table->string('marca', 20);
            $table->string('modelo', 20);
            $table->string('rntrc', 12)->nullable();

            $table->string('taf', 15)->nullable();
            $table->string('renavam', 12)->nullable();
            $table->string('numero_registro_estadual', 30)->nullable();

            $table->string('tipo', 2);
            $table->string('tipo_carroceria', 2);
            $table->string('tipo_rodado', 2);

            $table->string('tara', 10);
            $table->string('capacidade', 10);

            $table->string('proprietario_documento', 20);
            $table->string('proprietario_nome', 40);
            $table->string('proprietario_ie', 13);
            $table->string('proprietario_uf', 2);
            $table->integer('proprietario_tp');
            $table->integer('funcionario_id')->nullable();
            $table->boolean('status')->default(1);

            // alter table veiculos add column funcionario_id integer default null;
            // alter table veiculos add column status boolean default 1;
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('veiculos');
    }
};
