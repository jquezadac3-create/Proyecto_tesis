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
        // Formas de pago
        Schema::create('formas_pago', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 5)->unique();
            $table->string('forma_pago');
        });

        // Factura cabecera
        Schema::create('factura_cabecera', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('no action');
            $table->string('secuencia_factura');
            $table->dateTime('fecha'); 
            $table->foreignId('cliente_id')->constrained('cliente')->onDelete('no action');
            $table->foreignId('forma_pago')->constrained('formas_pago')->onDelete('no action');
            $table->decimal('subtotal15', 15, 4)->default(0);
            $table->decimal('subtotal5', 15, 4)->default(0);
            $table->decimal('subtotal0', 15, 4)->default(0);
            $table->decimal('descuento', 15, 4)->default(0);
            $table->decimal('iva15', 15, 4)->default(0);
            $table->decimal('iva5', 15, 4)->default(0);
            $table->decimal('ice', 15, 4)->default(0);
            $table->decimal('adicional', 15, 4)->default(0);
            $table->decimal('total_factura', 15, 4)->default(0);
        });

        // Factura detalle
        Schema::create('factura_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_id')->constrained('factura_cabecera')->onDelete('no action');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('no action');
            $table->string('nombre_producto');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 15, 4);
            $table->decimal('total', 15, 4);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factura_detalle');
        Schema::dropIfExists('factura_cabecera');
        Schema::dropIfExists('formas_pago');
    }
};
