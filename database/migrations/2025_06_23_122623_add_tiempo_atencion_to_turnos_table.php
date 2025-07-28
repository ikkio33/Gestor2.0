<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('turnos', function (Blueprint $table) {
            $table->integer('tiempo_atencion')->nullable()->after('tiempo_espera'); // en segundos
        });
    }

    public function down()
    {
        Schema::table('turnos', function (Blueprint $table) {
            $table->dropColumn('tiempo_atencion');
        });
    }
};
