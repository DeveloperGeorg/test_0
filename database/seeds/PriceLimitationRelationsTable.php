<?php

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;

class PriceLimitationRelationsTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('price_limitation_relations')->insert([
            'user_type_id' => 1,
            'car_type_id' => 1,
            'stage_id' => 0,
            'price_limitation_id' => 1,
        ]);
        DB::table('price_limitation_relations')->insert([
            'user_type_id' => 1,
            'car_type_id' => 2,
            'stage_id' => 0,
            'price_limitation_id' => 2,
        ]);
        DB::table('price_limitation_relations')->insert([
            'user_type_id' => 1,
            'car_type_id' => 2,
            'stage_id' => 1,
            'price_limitation_id' => 3,
        ]);
    }
}
