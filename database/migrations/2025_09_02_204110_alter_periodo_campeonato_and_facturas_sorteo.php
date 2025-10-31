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
         // Alterar tabla periodo_campeonato -> agregar columna status
        Schema::table('periodo_campeonato', function (Blueprint $table) {
            $table->enum('status', ['inactivo','activo','finalizado'])
                  ->default('inactivo')
                  ->after('fecha_fin');
        });

        // Alterar tabla facturas_sorteo -> agregar periodo_id con FK
        Schema::table('facturas_sorteo', function (Blueprint $table) {
            $table->unsignedBigInteger('periodo_id')->after('cantidad');
            $table->foreign('periodo_id', 'factura_periodo_fk')
                  ->references('id')
                  ->on('periodo_campeonato')
                  ->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir cambios en periodo_campeonato
        Schema::table('periodo_campeonato', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        // Revertir cambios en facturas_sorteo
        Schema::table('facturas_sorteo', function (Blueprint $table) {
            $table->dropForeign('factura_periodo_fk');
            $table->dropColumn('periodo_id');
        });
    }
};
