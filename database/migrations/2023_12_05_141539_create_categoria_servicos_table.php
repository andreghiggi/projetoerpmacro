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
        Schema::create('categoria_servicos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('nome', 50);

            $table->string('imagem', 25)->nullable();
            $table->boolean('marketplace')->default(0);
            $table->string('hash_delivery', 50)->nullable();

            // alter table categoria_servicos add column imagem varchar(25) default '';
            // alter table categoria_servicos add column marketplace boolean default 0;

            // alter table categoria_servicos add column hash_delivery varchar(50) default null;
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categoria_servicos');
    }
};
