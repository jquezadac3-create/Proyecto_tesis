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
        Schema::create('cliente', function (Blueprint $table) {
            $table->id();
            $table->string('nombres');
            $table->string('apellidos');
            $table->enum('tipo_identificacion', ['cedula', 'ruc', 'pasaporte']); // Clasificacion sri
            $table->string('numero_identificacion')->unique();
            $table->string('email')->unique();
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();
            $table->timestamp('fecha_creacion')->useCurrent(); // Base de datos poner en zona horaria Guayaquil/Ecuador
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cliente');
    }
};
