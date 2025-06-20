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
        Schema::create('contigencias', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->nullable()->constrained('empresas');
            $table->boolean('status');
            $table->enum('tipo', ['SVCAN', 'SVCRS', 'OFFLINE']);
            $table->string('motivo', 255);
            $table->text('status_retorno');
            $table->enum('documento', ['NFe', 'NFCe', 'CTe', 'MDFe']);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contigencias');
    }
};
