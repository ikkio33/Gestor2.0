<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTurnosTable extends Migration
{
    public function up()
    {
        Schema::create('turnos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('servicio_id');
            $table->unsignedBigInteger('meson_id')->nullable();
            $table->string('estado')->default('pendiente'); // Puede ser 'pendiente', 'atendido', etc.
            $table->integer('tiempo_espera')->nullable();  // Tiempo en minutos, o null si no está definido
            $table->string('turno'); // Número o código de turno
            $table->timestamps();

            // Relaciones
            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
            $table->foreign('servicio_id')->references('id')->on('servicios')->onDelete('cascade');
            $table->foreign('meson_id')->references('id')->on('mesones')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('turnos');
    }
}
