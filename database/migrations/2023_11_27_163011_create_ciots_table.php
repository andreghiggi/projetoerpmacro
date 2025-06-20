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
        Schema::create('ciots', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mdfe_id')->constrained('mdves');
            $table->string('cpf_cnpj', 18);
            $table->string('codigo', 20);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ciots');
    }
};
