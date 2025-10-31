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
            $table->string('nombre_periodo')->after('periodo_id')->nullable();
            $table->unsignedBigInteger('producto_id')->after('nombre_periodo')->nullable();
            $table->string('nombre_producto')->after('producto_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facturas_sorteo', function (Blueprint $table) {
            $table->dropColumn(['nombre_periodo', 'producto_id', 'nombre_producto']);
        });
    }
};
