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
        Schema::create('servico_os', function (Blueprint $table) {
            $table->id();

            $table->foreignId('servico_id')->nullable()->constrained('servicos');
            $table->foreignId('ordem_servico_id')->nullable()->constrained('ordem_servicos');
            $table->integer('quantidade');

            $table->boolean('status')->default(false);

            $table->decimal('valor', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);

            $table->timestamps();

            // alter table servico_os add column valor decimal(10, 2) default 0;
            // alter table servico_os add column subtotal decimal(10, 2) default 0;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servico_os');
    }
};
