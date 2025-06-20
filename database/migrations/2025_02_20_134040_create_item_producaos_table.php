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
        Schema::create('item_producaos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('produto_id')->constrained('produtos');
            $table->decimal('quantidade', 12,3);
            $table->boolean('status')->default(0);
            $table->integer('item_id')->default(0);
            $table->string('observacao', 100)->nullable();
            $table->string('dimensao', 100)->nullable();

            // alter table item_producaos add column observacao varchar(100) default null;
            // alter table item_producaos add column dimensao varchar(100) default null;

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_producaos');
    }
};
