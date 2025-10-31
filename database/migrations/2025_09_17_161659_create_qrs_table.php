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
        Schema::create('qrs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('factura_id')->unique()->constrained('factura_cabecera')->onDelete('no action');
            $table->foreignId('authorized_by')->constrained('users')->onDelete('no action');
            $table->json('qr_code_data');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qrs');
    }
};
