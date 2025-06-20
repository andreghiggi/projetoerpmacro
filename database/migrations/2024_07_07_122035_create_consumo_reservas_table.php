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
        Schema::create('consumo_reservas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('reserva_id')->constrained('reservas');
            $table->foreignId('produto_id')->nullable()->constrained('produtos');
            $table->decimal('quantidade', 8, 2);
            $table->decimal('valor_unitario', 12, 2);
            $table->decimal('sub_total', 12, 2);
            $table->string('observacao', 200)->nullable();
            $table->boolean('frigobar')->default(0);

            // alter table consumo_reservas add column frigobar boolean default 0;

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumo_reservas');
    }
};
