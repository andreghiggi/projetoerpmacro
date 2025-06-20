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
        Schema::create('item_agendamentos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('servico_id')->nullable()->constrained('servicos');
            $table->foreignId('agendamento_id')->nullable()->constrained('agendamentos');
            
            $table->integer('quantidade');
            $table->decimal('valor', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_agendamentos');
    }
};
