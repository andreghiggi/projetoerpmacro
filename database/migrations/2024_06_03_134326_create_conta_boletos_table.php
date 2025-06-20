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
        Schema::create('conta_boletos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->nullable()->constrained('empresas');

            $table->string('banco', 30);
            $table->string('agencia', 10);
            $table->string('conta', 15);
            $table->string('titular', 45);

            $table->boolean('padrao')->default(0);
            $table->boolean('usar_logo')->default(0);

            $table->string('documento', 18);
            $table->string('rua', 60);
            $table->string('numero', 10);
            $table->string('cep', 9);
            $table->string('bairro', 30);
            $table->foreignId('cidade_id')->nullable()->constrained('cidades');

            $table->string('carteira', 10)->nullable();
            $table->string('convenio', 20)->nullable();
            $table->decimal('juros', 10, 2)->nullable();
            $table->decimal('multa', 10, 2)->nullable();
            $table->integer('juros_apos')->nullable();
            $table->enum('tipo', ['Cnab400', 'Cnab240']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conta_boletos');
    }
};
