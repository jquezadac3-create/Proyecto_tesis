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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->integer('cantidad')->default(0);
            $table->enum('tipo_producto', ['producto', 'servicio'])->default('producto'); // Clasificacion sri
            $table->decimal('precio_venta_sin_iva', 10, 2); // Precio sin impuesto
            $table->decimal('precio_venta_final', 10, 2); // Precio con impuesto
            $table->decimal('costo', 10, 2)->nullable(); // Costo del producto 
            $table->foreignId('categoria_id')->constrained('categoria_productos')->onDelete('no action');
            $table->boolean('abono')->default(false);
            $table->foreignId('id_abono')->nullable()->constrained('abonos')->onDelete('no action');
            $table->timestamp('fecha_creacion')->useCurrent();
        });
         
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
