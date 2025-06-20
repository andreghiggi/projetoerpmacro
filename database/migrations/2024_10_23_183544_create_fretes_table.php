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
        Schema::create('fretes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('veiculo_id')->constrained('veiculos');
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->integer('numero_sequencial')->nullable();

            $table->enum('estado', ['em_carregamento', 'em_viagem', 'finalizado']);
            $table->string('observacao', 200)->nullable();
            $table->decimal('total', 12,2);
            $table->decimal('total_despesa', 12,2)->nullable();
            $table->decimal('desconto', 10,2)->nullable();
            $table->decimal('acrescimo', 10,2)->nullable();
            $table->integer('local_id')->nullable();
            $table->integer('conta_receber_id')->nullable();

            $table->foreignId('cidade_carregamento')->constrained('cidades');
            $table->foreignId('cidade_descarregamento')->constrained('cidades');

            $table->decimal('distancia_km', 10,2)->nullable();
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();

            $table->time('horario_inicio')->nullable();
            $table->time('horario_fim')->nullable();

            // alter table fretes add column numero_sequencial integer default null;
            // alter table fretes add column cidade_carregamento integer default null;
            // alter table fretes add column cidade_descarregamento integer default null;
            // alter table fretes add column total_despesa decimal(12,2) default null;

            // alter table fretes modify column horario_inicio time default null;
            // alter table fretes modify column horario_fim time default null;
            // alter table fretes add column conta_receber_id integer default null;

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fretes');
    }
};
