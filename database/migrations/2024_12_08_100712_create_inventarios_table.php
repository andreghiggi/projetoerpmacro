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
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->nullable()->constrained('empresas');
            $table->foreignId('usuario_id')->nullable()->constrained('users');

            $table->date('inicio');
            $table->date('fim');
            $table->boolean('status');

            $table->string('referencia', 30);
            $table->string('observacao', 255)->nullable();
            $table->string('tipo', 15);
            $table->integer('numero_sequencial')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventarios');
    }
};
