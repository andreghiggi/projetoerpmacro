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
        Schema::create('funcionario_os', function (Blueprint $table) {
            $table->id();

            $table->foreignId('usuario_id')->nullable()->constrained('users');
            $table->foreignId('ordem_servico_id')->nullable()->constrained('ordem_servicos');
            $table->foreignId('funcionario_id')->nullable()->constrained('funcionarios');

            $table->string('funcao');    

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funcionario_os');
    }
};
