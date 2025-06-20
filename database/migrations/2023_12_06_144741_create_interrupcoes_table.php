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
        Schema::create('interrupcoes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('funcionario_id')->nullable()->constrained('funcionarios');
            $table->time('inicio');
            $table->time('fim');
            $table->enum('dia_id', ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo']);

            $table->string('motivo', 50);
            $table->foreignId('empresa_id')->nullable()->constrained('empresas');
            $table->boolean('status')->default(1);

            // alter table interrupcoes add column motivo varchar(50) default null;
            // alter table interrupcoes add column status boolean default 1;

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interrupcoes');
    }
};
