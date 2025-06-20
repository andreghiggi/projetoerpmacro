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
        Schema::create('info_descargas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mdfe_id')->constrained('mdves');

            $table->foreignId('cidade_id')->constrained('cidades');

            $table->integer('tp_unid_transp');
            $table->string('id_unid_transp', 20);
            $table->decimal('quantidade_rateio', 5, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('info_descargas');
    }
};
