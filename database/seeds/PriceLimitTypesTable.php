<?php

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;

class PriceLimitTypesTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('price_limit_types')->insert([
            'code' => 'max_minute_price_full',
        ]);
        DB::table('price_limit_types')->insert([
            'code' => 'max_minute_price',
        ]);
    }
}
