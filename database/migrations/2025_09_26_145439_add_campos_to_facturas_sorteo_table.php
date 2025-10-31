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
        Schema::table('facturas_sorteo', function (Blueprint $table) {
            // quitar unique de numero_factura
            $table->dropUnique('facturas_sorteo_numero_factura_unique');

            // nuevos campos
            $table->unsignedBigInteger('jornada_id')->nullable()->after('periodo_id');
            $table->string('nombre_jornada')->nullable()->after('jornada_id');

            $table->unsignedBigInteger('abono_id')->nullable()->after('nombre_jornada');
            $table->string('nombre_abono')->nullable()->after('abono_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facturas_sorteo', function (Blueprint $table) {
            // Eliminar columnas
            $table->dropColumn([
                'jornada_id',
                'nombre_jornada',
                'abono_id',
                'nombre_abono',
            ]);

            // restaurar unique constraint en numero_factura
            $table->unique('numero_factura', 'facturas_sorteo_numero_factura_unique');
        });
    }
};
