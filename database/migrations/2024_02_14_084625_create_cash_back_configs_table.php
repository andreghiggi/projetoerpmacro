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
        Schema::create('cash_back_configs', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->decimal('valor_percentual', 5, 2);
            $table->integer('dias_expiracao');
            $table->decimal('valor_minimo_venda', 10, 2);
            $table->decimal('percentual_maximo_venda', 10, 2);
            $table->string('mensagem_padrao_whatsapp', 255);
            $table->text('mensagem_automatica_5_dias');
            $table->text('mensagem_automatica_1_dia');

            // alter table cash_back_configs add column mensagem_automatica_5_dias text;
            // alter table cash_back_configs add column mensagem_automatica_1_dia text;

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_back_configs');
    }
};
