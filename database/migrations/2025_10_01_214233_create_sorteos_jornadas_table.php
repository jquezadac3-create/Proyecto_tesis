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
        Schema::create('sorteos_jornadas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sorteo_id')
                  ->constrained('sorteos')
                  ->onDelete('cascade');
            $table->foreignId('jornada_id')
                  ->constrained('jornadas')
                  ->onDelete('cascade');
            $table->timestamps();

            $table->unique(['sorteo_id', 'jornada_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sorteos_jornadas');
    }
};
