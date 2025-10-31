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
        Schema::create('productos_jornada', function (Blueprint $table) {
            // Foreign keys
            $table->foreignId('id_producto')
                ->constrained('productos')
                ->onDelete('no action');

            $table->foreignId('id_jornada')
                ->constrained('jornadas')
                ->onDelete('no action');

            // Campo extra
            $table->integer('stock')->default(0);

            // Definir primary key compuesta
            $table->primary(['id_producto', 'id_jornada']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos_jornada');
    }
};
