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
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('acomodacao_id')->constrained('acomodacaos');
            $table->integer('numero_sequencial')->nullable();

            $table->date('data_checkin');
            $table->date('data_checkout');

            $table->decimal('valor_estadia', 12, 2);
            $table->decimal('desconto', 12, 2)->nullable();
            $table->decimal('valor_outros', 12, 2)->nullable();
            $table->decimal('valor_total', 12, 2)->nullable();

            $table->enum('estado', ['pendente', 'iniciado', 'finalizado', 'cancelado']);
            $table->text('observacao');
            $table->text('motivo_cancelamento');

            $table->boolean('conferencia_frigobar')->default(0);

            $table->integer('total_hospedes')->nullable();

            $table->string('codigo_reseva', 25);
            $table->string('link_externo', 255);
            $table->timestamp('data_checkin_realizado')->nullable();
            $table->integer('nfe_id')->nullable();
            $table->integer('nfse_id')->nullable();

            // alter table reservas add column numero_sequencial integer default null;
            // alter table reservas add column nfe_id integer default null;
            // alter table reservas add column nfse_id integer default null;
            // alter table reservas add column data_checkin_realizado timestamp default CURRENT_TIMESTAMP;
            // alter table reservas add column motivo_cancelamento text;

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
