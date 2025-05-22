<?php



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMesonServicioTable extends Migration
{
    public function up()
    {//
       // Schema::create('meson_servicio', function (Blueprint $table) {
       //     $table->id();
       //     $table->foreignId('meson_id')->constrained('mesones')->onDelete('set null');
       //     $table->foreignId('servicio_id')->constrained('servicios')->onDelete('set null');
       //     $table->timestamps();
       // });
    }


    public function down()
    {
        //Schema::dropIfExists('meson_servicio');
    }
}
