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
        Schema::create('sorteos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('periodo_id');
            $table->string('nombre'); // nombre del sorteo
            $table->integer('num_premios'); // cuántos premios tiene este sorteo
            $table->integer('posicion_ganadora'); // ej: 5 -> el 5to boleto es el ganador
            $table->timestamps();

            $table->index('periodo_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sorteos');
    }
};
