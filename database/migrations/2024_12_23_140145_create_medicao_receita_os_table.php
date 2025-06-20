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
        Schema::create('medicao_receita_os', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ordem_servico_id')->constrained('ordem_servicos');
            $table->string('esferico_longe_od', 10)->nullable();
            $table->string('esferico_longe_oe', 10)->nullable();
            $table->string('esferico_perto_od', 10)->nullable();
            $table->string('esferico_perto_oe', 10)->nullable();

            $table->string('cilindrico_longe_od', 10)->nullable();
            $table->string('cilindrico_longe_oe', 10)->nullable();
            $table->string('cilindrico_perto_od', 10)->nullable();
            $table->string('cilindrico_perto_oe', 10)->nullable();

            $table->string('eixo_longe_od', 10)->nullable();
            $table->string('eixo_longe_oe', 10)->nullable();
            $table->string('eixo_perto_od', 10)->nullable();
            $table->string('eixo_perto_oe', 10)->nullable();

            $table->string('altura_longe_od', 10)->nullable();
            $table->string('altura_longe_oe', 10)->nullable();
            $table->string('altura_perto_od', 10)->nullable();
            $table->string('altura_perto_oe', 10)->nullable();

            $table->string('dnp_longe_od', 10)->nullable();
            $table->string('dnp_longe_oe', 10)->nullable();
            $table->string('dnp_perto_od', 10)->nullable();
            $table->string('dnp_perto_oe', 10)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicao_receita_os');
    }
};
