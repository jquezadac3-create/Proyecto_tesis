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
        Schema::table('factura_cabecera', function (Blueprint $table) {
            // Agregar columna periodo_id (relacionada a otra tabla si aplica)
            $table->unsignedBigInteger('periodo_id')->default(1)->after('forma_pago');
 
            // Agregar columna status
            $table->enum('status', ['valida', 'anulada'])->default('valida')->after('periodo_id');

            // Agregar clave foránea
            $table->foreign('periodo_id')->references('id')->on('periodo_campeonato');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('factura_cabecera', function (Blueprint $table) {
            $table->dropForeign(['periodo_id']);
            $table->dropColumn('periodo_id');
            $table->dropColumn('status');
        });
    }
};
