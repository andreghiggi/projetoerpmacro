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
        Schema::table('produtos', function (Blueprint $table) {
            $table->unsignedBigInteger('conecta_venda_id')->nullable()->index();
            $table->boolean('conecta_venda_status')->default(false);
            $table->timestamp('conecta_venda_data_publicacao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->unsignedBigInteger('conecta_venda_id')->nullable()->index();
            $table->boolean('conecta_venda_status')->default(false);
            $table->timestamp('conecta_venda_data_publicacao');
        });
    }
};
