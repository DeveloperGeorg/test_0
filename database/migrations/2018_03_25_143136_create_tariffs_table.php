<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTariffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tariffs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('time_start');
            $table->integer('time_end');
            $table->integer('minute_start');
            $table->integer('minute_end');
            $table->integer('distance_start');
            $table->integer('distance_end');
            $table->float('price');
            $table->integer('price_type_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tariffs');
    }
}
