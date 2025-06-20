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
        Schema::create('ticket_mensagem_anexos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ticket_mensagem_id')->constrained('ticket_mensagems');
            $table->string('anexo', 25)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_mensagem_anexos');
    }
};
