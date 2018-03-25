<?php

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;

class CarTypesTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('car_types')->insert([
            'code' => 'default',
        ]);
        DB::table('car_types')->insert([
            'code' => 'with_child',
        ]);
    }
}
