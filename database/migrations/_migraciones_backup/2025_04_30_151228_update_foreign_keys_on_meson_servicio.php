<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateForeignKeysOnMesonServicio extends Migration
{
   
    public function up()
{
    Schema::create('meson_servicio', function (Blueprint $table) {
       // $table->id();
       // $table->unsignedBigInteger('meson_id');
       // $table->unsignedBigInteger('servicio_id');
       // $table->primary('id');
       // 
       // // Definir las claves foráneas
       // $table->foreign('meson_id')->references('id')->on('meson')->onDelete('set null');
       // $table->foreign('servicio_id')->references('id')->on('servicios')->onDelete('cascade');
       // 
       // $table->timestamps();
    });
}


public function down()
{
    Schema::table('meson_servicio', function (Blueprint $table) {
        
       // $table->dropForeign('meson_servicio_ibfk_1'); 
       // $table->dropForeign('meson_servicio_ibfk_2'); 
//
       // $table->foreign('meson_id')->references('id')->on('meson')->onDelete('cascade');
       // $table->foreign('servicio_id')->references('id')->on('servicios')->onDelete('cascade');
    });
}

}
