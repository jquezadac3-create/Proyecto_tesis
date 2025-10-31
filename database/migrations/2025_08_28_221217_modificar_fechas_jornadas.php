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
        Schema::table('jornadas', function (Blueprint $table) {
            Schema::table('jornadas', function (Blueprint $table) {
                $table->dateTime('fecha_inicio')->change();
                $table->dateTime('fecha_fin')->change();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jornadas', function (Blueprint $table) {
            Schema::table('jornadas', function (Blueprint $table) {
                $table->date('fecha_inicio')->change();
                $table->date('fecha_fin')->change();
            });
        });
    }
};
