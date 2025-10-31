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
        Schema::create('boletos_sorteo', function (Blueprint $table) {
             $table->bigIncrements('id');

            // Relación con factura cabecera
            $table->unsignedBigInteger('factura_id'); 
            $table->string('numero_factura'); 
            $table->string('nombre_cliente');

            // Relación con periodo y producto
            $table->unsignedBigInteger('periodo_id');
            $table->unsignedBigInteger('producto_id')->nullable();
            $table->string('nombre_producto')->nullable();
            $table->unsignedBigInteger('jornada_id')->nullable();
            $table->string('nombre_jornada')->nullable();
            $table->unsignedBigInteger('abono_id')->nullable();
            $table->string('nombre_abono')->nullable();

            // Datos del boleto
            $table->bigInteger('numero_boleto'); // secuencial por periodo
            $table->boolean('es_ganador')->default(false);
            $table->string('premio_ganado')->nullable();
            $table->boolean('ya_participo')->default(false);

            // Índices
            $table->index('periodo_id', 'boleto_periodo_idx');
            $table->index('factura_id', 'boleto_factura_idx');

            // Foreign key a factura_cabecera
            $table->foreign('factura_id')
                ->references('id')
                ->on('factura_cabecera')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boletos_sorteo');
    }
};
