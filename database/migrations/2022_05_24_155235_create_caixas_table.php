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
        Schema::create('caixas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->nullable()->constrained('empresas');
            $table->foreignId('usuario_id')->nullable()->constrained('users');

            $table->decimal('valor_abertura', 10, 2);
            $table->integer('conta_empresa_id')->nullable();
            $table->string('observacao', 200);
            $table->boolean('status')->default(0);
            $table->timestamp('data_fechamento')->nullable();
            
            $table->decimal('valor_fechamento', 10, 2)->nullable();
            $table->decimal('valor_dinheiro', 10, 2)->nullable();
            $table->decimal('valor_cheque', 10, 2)->nullable();
            $table->decimal('valor_outros', 10, 2)->nullable();
            $table->integer('local_id')->nullable();

            // alter table caixas add column data_fechamento timestamp default null;

            // alter table caixas add column valor_fechamento decimal(10,2) default 0;
            // alter table caixas add column valor_dinheiro decimal(10,2) default 0;
            // alter table caixas add column valor_cheque decimal(10,2) default 0;
            // alter table caixas add column valor_outros decimal(10,2) default 0;
            // alter table caixas add column conta_empresa_id integer default null;
            // alter table caixas add column local_id integer default null;


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caixas');
    }
};
