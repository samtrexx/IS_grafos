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
        //cambiar el nombre
        Schema::create('tarjeta_creditos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('no_tarjeta');
            $table->string('tipo');
            $table->decimal('limite',10,2); //monto
            $table->date('fecha_corte');
            $table->decimal('tasa_interes',6,2)->nullable();
            $table->text('observaciones')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarjeta_creditos');
    }
};
