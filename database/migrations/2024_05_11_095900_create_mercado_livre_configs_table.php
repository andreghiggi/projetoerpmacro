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
        Schema::create('mercado_livre_configs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas');
            $table->string('client_id', 30);
            $table->string('client_secret', 100);
            $table->string('access_token', 255)->nullable();
            $table->string('refresh_token', 255)->nullable();
            $table->string('user_id', 25)->nullable();
            $table->string('code', 100)->nullable();
            $table->string('url', 120);
            $table->bigInteger('token_expira');
            $table->timestamps();

            // alter table mercado_livre_configs add column refresh_token varchar(255) default null;
            // alter table mercado_livre_configs add column token_expira bigint default null;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mercado_livre_configs');
    }
};
