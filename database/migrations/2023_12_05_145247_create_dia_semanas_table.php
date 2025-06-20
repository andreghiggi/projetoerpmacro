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
        Schema::create('dia_semanas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('funcionario_id')->nullable()->constrained('funcionarios');
            $table->text('dia');
            $table->foreignId('empresa_id')->nullable()->constrained('empresas');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dia_semanas');
    }
};
