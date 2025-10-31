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
        Schema::table('productos', function (Blueprint $table) {
            $table->decimal('precio_venta_sin_iva', 10, 4)->change();
            $table->decimal('precio_venta_final', 10, 4)->change();
            $table->decimal('costo', 10, 4)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->decimal('precio_venta_sin_iva', 10, 2)->change();
            $table->decimal('precio_venta_final', 10, 2)->change();
            $table->decimal('costo', 10, 2)->nullable()->change();
        });
    }
};
