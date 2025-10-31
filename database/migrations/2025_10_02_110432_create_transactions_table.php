<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Additional info:
     * status code:
     * -1: Pendiente
     * 2: Cancelado
     * 3: Aprobado
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('clientTransactionId')->unique();
            $table->json('invoice_data');
            $table->json('response_data')->nullable();
            $table->enum('status', [-1, 2, 3])->default(-1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
