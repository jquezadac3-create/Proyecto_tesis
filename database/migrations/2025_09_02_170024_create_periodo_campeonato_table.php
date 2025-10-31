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
        Schema::create('periodo_campeonato', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->comment('Nombre del periodo o campeonato');
            $table->date('fecha_inicio')->comment('Fecha de inicio del periodo');
            $table->date('fecha_fin')->comment('Fecha de fin del periodo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periodo_campeonato');
    }
};
