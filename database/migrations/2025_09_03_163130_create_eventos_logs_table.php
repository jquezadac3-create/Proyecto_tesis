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
        Schema::create('eventos_logs', function (Blueprint $table) {
            $table->id();
            $table->string('evento'); // nombre del evento
            $table->unsignedInteger('filas_afectadas')->default(0); 
            $table->timestamp('ejecutado_en')->useCurrent(); // fecha y hora
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos_logs');
    }
};
