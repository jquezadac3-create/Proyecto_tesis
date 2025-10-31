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
        if (!Schema::hasColumn('productos_jornada', 'stock_actual')) {
            Schema::table('productos_jornada', function (Blueprint $table) {
                $table->integer('stock_actual')
                    ->default(0)
                    ->after('stock')
                    ->comment('Stock actual disponible para ventas');
            });
        }

        // Modificar enum tipo_movimiento en movimientos_stock
        DB::statement("
            ALTER TABLE `movimientos_stock`
            MODIFY `tipo_movimiento` ENUM('ingreso','egreso') 
            COLLATE utf8mb4_unicode_ci 
            NOT NULL DEFAULT 'ingreso'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar columna stock_actual si existe
        if (Schema::hasColumn('productos_jornada', 'stock_actual')) {
            Schema::table('productos_jornada', function (Blueprint $table) {
                $table->dropColumn('stock_actual');
            });
        }

        // Revertir enum tipo_movimiento a los valores originales
        DB::statement("
            ALTER TABLE `movimientos_stock`
            MODIFY `tipo_movimiento` ENUM('entrada','salida') 
            COLLATE utf8mb4_unicode_ci 
            NOT NULL DEFAULT 'entrada'
        ");
    }
};
