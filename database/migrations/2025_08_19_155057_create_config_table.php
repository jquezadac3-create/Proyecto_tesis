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
        Schema::create('config', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('razon_social');
            $table->text('nombre_comercial');
            $table->text('ruc');
            $table->text('codigo_establecimiento');
            $table->text('serie_ruc');
            $table->text('direccion_matriz');
            $table->text('direccion_establecimiento');
            $table->text('tipo_contribuyente');
            $table->enum('obligado_contabilidad', ['SI', 'NO'])->default('NO');
            $table->enum('ambiente', ['PRODUCCION', 'PRUEBAS']);
            $table->boolean('estado_electronica')->default(true);
            $table->text('firma_contrasenia');
            $table->text('firma_path');
            $table->text('logo_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('config');
    }
};
