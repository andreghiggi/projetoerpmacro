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
        Schema::create('formato_armacao_oticas', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('empresa_id')->nullable()->constrained('empresas');
            $table->string('nome', 200);
            $table->boolean('status')->default(1);
            $table->string('imagem', 25)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formato_armacao_oticas');
    }
};
