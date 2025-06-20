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
        Schema::create('boletos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->nullable()->constrained('empresas');
            $table->foreignId('conta_boleto_id')->nullable()->constrained('conta_boletos');
            $table->foreignId('conta_receber_id')->nullable()->constrained('conta_recebers');

            $table->string('numero', 10);
            $table->string('numero_documento', 10);
            $table->string('carteira', 10);
            $table->string('convenio', 20);
            $table->date('vencimento');
            $table->decimal('valor', 12,2);
            $table->string('instrucoes', 255)->nullable();

            $table->string('linha_digitavel', 50)->nullable();
            $table->string('nome_arquivo', 35)->nullable();

            $table->decimal('juros', 10, 2)->nullable();
            $table->decimal('multa', 10, 2)->nullable();
            $table->integer('juros_apos')->nullable();

            $table->enum('tipo', ['Cnab400', 'Cnab240']);
            $table->boolean('usar_logo')->default(0);
            $table->string('posto', 10)->nullable();
            $table->string('codigo_cliente', 10)->nullable();

            $table->integer('valor_tarifa')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boletos');
    }
};
