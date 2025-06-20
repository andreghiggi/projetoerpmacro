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
        Schema::create('funcionarios', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->nullable()->constrained('empresas');
            $table->string('nome', 60);
            $table->string('cpf_cnpj', 20)->nullable();
            $table->string('telefone', 20)->nullable();
            $table->foreignId('cidade_id')->nullable()->constrained('cidades');
            $table->string('rua', 60)->nullable();
            $table->string('cep', 9)->nullable();
            $table->string('numero', 10)->nullable();
            $table->string('bairro', 40)->nullable();
            $table->foreignId('usuario_id')->nullable()->constrained('users');
            $table->decimal('comissao', 10, 2)->nullable();
            $table->decimal('salario', 10, 2)->default(0);
            $table->string('codigo', 30)->nullable();
            $table->boolean('status')->default(1);

            $table->boolean('permite_alterar_valor_app')->default(1);

            $table->timestamps();

            // alter table funcionarios add column comissao decimal(10 ,2) default 0;
            // alter table funcionarios add column salario decimal(10 ,2) default 0;
            // alter table funcionarios add column codigo varchar(30) default null;
            // alter table funcionarios add column status boolean default 1;

            // alter table funcionarios add column permite_alterar_valor_app boolean default 1;
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funcionarios');
    }
};
