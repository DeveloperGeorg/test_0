<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePriceLimitationRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_limitation_relations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_type_id');
            $table->integer('car_type_id');
            $table->integer('stage_id');
            $table->integer('price_limitation_id');
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
        Schema::dropIfExists('price_limitation_relations');
    }
}
