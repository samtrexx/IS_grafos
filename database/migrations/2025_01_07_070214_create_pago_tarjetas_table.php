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
        Schema::create('pago_tarjetas', function (Blueprint $table) {
            $table->id();
            $table->string('tarjeta_numero');
            $table->float('Monto', 10, 2);
            $table->enum('tipo_liquidacion', ['pago', 'ajuste', 'reembolso']);
            $table->string('estado')->default('pendiente');
            $table->date('fecha_liquidacion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pago_tarjetas');
    }
};
