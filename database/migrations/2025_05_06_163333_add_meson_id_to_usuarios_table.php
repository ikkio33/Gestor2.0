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
    Schema::table('usuarios', function (Blueprint $table) {
        $table->unsignedBigInteger('meson_id')->nullable(); // Asegúrate de que sea el tipo correcto
    });
}

public function down()
{
    Schema::table('usuarios', function (Blueprint $table) {
        $table->dropColumn('meson_id');
    });
}

};
