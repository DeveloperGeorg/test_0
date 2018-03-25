<?php

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;

class TariffsTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        //
        DB::table('tariffs')->insert([
            'time_start' => 0,
            'time_end' => 419,
            'minute_start' => 0,
            'minute_end' => 0,
            'distance_start' => 0,
            'distance_end' => 0,
            'price' => 0.0,
            'price_type_id' => 2,
        ]);
        DB::table('tariffs')->insert([
            'time_start' => 420,
            'time_end' => 1439,
            'minute_start' => 0,
            'minute_end' => 20,
            'distance_start' => 0,
            'distance_end' => 0,
            'price' => 0.0,
            'price_type_id' => 2,
        ]);
        DB::table('tariffs')->insert([
            'time_start' => 420,
            'time_end' => 1439,
            'minute_start' => 20,
            'minute_end' => 0,
            'distance_start' => 0,
            'distance_end' => 0,
            'price' => 2.0,
            'price_type_id' => 2,
        ]);
        DB::table('tariffs')->insert([
            'time_start' => 0,
            'time_end' => 419,
            'minute_start' => 0,
            'minute_end' => 0,
            'distance_start' => 0,
            'distance_end' => 0,
            'price' => 2.0,
            'price_type_id' => 2,
        ]);
    }
}
