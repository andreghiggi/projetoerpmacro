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
        Schema::create('bairro_deliveries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas');
            $table->string('nome', 60);
            $table->decimal('valor_entrega', 10, 2);
            $table->integer('bairro_delivery_super')->nullable();
            $table->boolean('status')->default(1);

            // alter table bairro_deliveries add column status boolean default 1;

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bairro_deliveries');
    }
};
