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
        Schema::create('item_dimensao_nves', function (Blueprint $table) {
            $table->id();

            $table->foreignId('item_nfe_id')->nullable()->constrained('item_nves');
            $table->decimal('valor_unitario_m2', 12, 2);
            $table->decimal('largura', 12, 2);
            $table->decimal('altura', 12, 2);
            $table->decimal('quantidade', 12, 2);
            $table->decimal('m2_total', 12, 2);
            $table->decimal('sub_total', 12, 2);
            $table->decimal('espessura', 12, 2);
            $table->string('observacao', 200);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_dimensao_nves');
    }
};
