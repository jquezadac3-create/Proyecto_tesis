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
       // Agregar columnas a factura_detalle
        Schema::table('factura_detalle', function (Blueprint $table) {
            $table->unsignedBigInteger('abono_id')->nullable()->after('producto_id');
            $table->unsignedBigInteger('jornada_id')->nullable()->after('abono_id');

            // Relaciones (si existen las tablas abonos y jornadas)
            $table->foreign('abono_id')->references('id')->on('abonos')->onDelete('set null');
            $table->foreign('jornada_id')->references('id')->on('jornadas')->onDelete('set null');
        });

        // Cambiar ENUM en jornadas 
        DB::statement("ALTER TABLE jornadas MODIFY estado ENUM('activa', 'inactiva', 'finalizada') NOT NULL DEFAULT 'activa'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir cambios en factura_detalle
        Schema::table('factura_detalle', function (Blueprint $table) {
            $table->dropForeign(['abono_id']);
            $table->dropForeign(['jornada_id']);
            $table->dropColumn(['abono_id', 'jornada_id']);
        });

        // Revertir ENUM a original
        DB::statement("ALTER TABLE jornadas MODIFY estado ENUM('activa', 'inactiva') NOT NULL DEFAULT 'activa'");
    }
};
