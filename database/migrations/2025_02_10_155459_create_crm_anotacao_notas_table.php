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
        Schema::create('crm_anotacao_notas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('crm_anotacao_id')->constrained('crm_anotacaos');
            $table->text('nota');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_anotacao_notas');
    }
};
