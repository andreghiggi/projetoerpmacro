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
        Schema::create('item_inventarios', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inventario_id')->nullable()->constrained('inventarios');
            $table->foreignId('produto_id')->nullable()->constrained('produtos');
            $table->foreignId('usuario_id')->nullable()->constrained('users');

            $table->decimal('quantidade', 10,2);
            $table->string('observacao', 100)->nullable();
            $table->string('estado', 15);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_inventarios');
    }
};
