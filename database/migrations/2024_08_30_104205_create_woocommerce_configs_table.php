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
        Schema::create('woocommerce_configs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas');

            $table->string('consumer_key', 255);
            $table->string('consumer_secret', 255);
            $table->string('url', 255);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('woocommerce_configs');
    }
};
