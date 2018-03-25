<?php

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;

class PriceTypesTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('price_types')->insert([
            'code' => 'price_once',
        ]);
        DB::table('price_types')->insert([
            'code' => 'price_minute',
        ]);
        DB::table('price_types')->insert([
            'code' => 'price_distance',
        ]);
        DB::table('price_types')->insert([
            'code' => 'max_price',
        ]);
    }
}
