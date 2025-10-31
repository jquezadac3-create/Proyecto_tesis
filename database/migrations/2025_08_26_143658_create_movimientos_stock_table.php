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
        Schema::create('movimientos_stock', function (Blueprint $table) {
            $table->id();
            
            // Producto al que pertenece el movimiento
            $table  ->foreignId('producto_id')
                    ->constrained('productos')
                    ->onDelete('cascade');

            // Usuario que realizó el movimiento
            $table  ->foreignId('user_id')
                    ->constrained('users')
                    ->onDelete('cascade');

            // Jornada asociada (opcional)
            $table  ->foreignId('jornada_id')
                    ->nullable()
                    ->constrained('jornadas')
                    ->nullOnDelete();
            
            // Tipo movimiento
            $table->enum('tipo_movimiento', ['entrada', 'salida'])->default('entrada');
            
            // Stock antes y después
            $table->integer('stock_anterior')->default(0);
            $table->integer('stock_agregado')->default(0);
            $table->integer('stock_nuevo')->default(0);

            // Motivo o nota
            $table->string('motivo')->nullable();

            // Fecha del movimiento
            $table->timestamp('fecha')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_stock');
    }
};
