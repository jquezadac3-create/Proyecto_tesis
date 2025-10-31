<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * La siguiente migracion fue creada para eliminar la restriccion de unicidad en la columna email de la tabla cliente.
 * Se ha realizado de la siguiente manera debido a que al momento de realizar la compra de un ticket de manera online, un cliente o varios podrian compartir la misma direccion de correo electronico.
 * Esto es comun en situaciones donde familiares o amigos compran tickets juntos y utilizan una direccion de correo electronico compartida para recibir las facturas.
 * Al eliminar la restriccion de unicidad, se facilita el proceso de compra y se mejora la experiencia del usuario.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /**
         * Remove the unique constraint from the email column in cliente table
         */
        Schema::table('cliente', function (Blueprint $table) {
            $table->dropUnique('cliente_email_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cliente', function (Blueprint $table) {
            $table->unique('email');
        });
    }
};
