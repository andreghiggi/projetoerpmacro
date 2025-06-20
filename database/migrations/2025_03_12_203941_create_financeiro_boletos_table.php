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
        Schema::create('financeiro_boletos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');

            $table->decimal('valor', 10, 2);
            $table->decimal('valor_recebido', 10, 2);
            $table->decimal('juros', 10, 2);
            $table->decimal('multa', 10, 2);
            $table->date('vencimento');
            $table->date('data_recebimento')->nullable();
            $table->string('pdf_boleto', 255)->nullable();
            $table->boolean('status');
            $table->integer('plano_id')->nullable();
            $table->date('data_liquidacao')->nullable();
            $table->string('_id', 30);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financeiro_boletos');
    }
};
