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
        Schema::table('boletos_sorteo', function (Blueprint $table) {
            $table->unsignedBigInteger('sorteo_id')->nullable()->after('ya_participo');

            $table->foreign('sorteo_id')
                  ->references('id')
                  ->on('sorteos')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boletos_sorteo', function (Blueprint $table) {
            $table->dropForeign(['sorteo_id']);
            $table->dropColumn('sorteo_id');
        });
    }
};
