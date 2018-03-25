<?php

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;

class PriceLimitationsTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('price_limitations')->insert([
            'price_limit_type_id' => 1,
            'distance_start' => 0,
            'distance_end' => 70,
            'price' => 2700.0,
        ]);
        DB::table('price_limitations')->insert([
            'price_limit_type_id' => 1,
            'distance_start' => 0,
            'distance_end' => 70,
            'price' => 3000.0,
        ]);
        DB::table('price_limitations')->insert([
            'price_limit_type_id' => 2,
            'distance_start' => 0,
            'distance_end' => 70,
            'price' => 10.0,
        ]);
    }
}
