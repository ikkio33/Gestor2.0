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
        //Schema::create('usuarios', function (Blueprint $table) {
        //    $table->id();
        //    $table->string('nombre');
        //    $table->string('correo')->unique();
        //    $table->string('password'); // Usaremos 'clave' en vez de 'password'
        //    $table->enum('rol', ['administrador', 'funcionario', 'soporte']);
        //    $table->timestamps();
        //});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //Schema::dropIfExists('usuarios');
    }
};
