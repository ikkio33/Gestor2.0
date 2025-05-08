<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class RenameClientesToComparecientes extends Migration
{
    public function up()
    {
        Schema::rename('clientes', 'comparecientes');
    }

    public function down()
    {
        Schema::rename('comparecientes', 'clientes');
    }
}

