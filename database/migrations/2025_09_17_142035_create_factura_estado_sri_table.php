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
        Schema::create('factura_estado_sri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_cabecera_id')->constrained('factura_cabecera')->onDelete('no action');
            $table->string('clave_acceso', 49)->unique();
            $table->enum('estado_recepcion', ['PENDIENTE', 'RECIBIDA', 'DEVUELTA'])->default('PENDIENTE');
            $table->enum('estado_autorizacion', ['PENDIENTE', 'AUTORIZADO', 'RECHAZADO'])->default('PENDIENTE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factura_estado_sri');
    }
};
