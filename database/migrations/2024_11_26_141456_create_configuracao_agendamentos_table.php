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
        Schema::create('configuracao_agendamentos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas');
            $table->string('token_whatsapp', 120)->nullable();
            $table->integer('tempo_descanso_entre_agendamento')->default(0);
            $table->boolean('msg_wpp_manha')->default(0);
            $table->string('msg_wpp_manha_horario', 5)->nullable();

            $table->boolean('msg_wpp_alerta')->default(0);
            $table->integer('msg_wpp_alerta_minutos_antecedencia')->nullable();
            
            $table->text('mensagem_manha');
            $table->text('mensagem_alerta');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracao_agendamentos');
    }
};
