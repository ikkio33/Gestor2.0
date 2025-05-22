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
        Schema::table('turnos', function (Blueprint $table) {
            $table->string('codigo_seguimiento')->nullable()->after('id');
            $table->string('estado')->default('pendiente')->after('codigo_seguimiento');
            $table->string('tipo')->default('normal')->after('estado');
            $table->string('tipo_llamado')->default('normal')->after('tipo');
            $table->integer('llamado_numero')->nullable()->after('tipo_llamado');
            $table->integer('llamado_numero_espera')->nullable()->after('llamado_numero');
            $table->integer('llamado_numero_atendido')->nullable()->after('llamado_numero_espera');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('turnos', function (Blueprint $table) {
            //
        });
    }
};
