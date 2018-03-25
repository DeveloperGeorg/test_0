<?php

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;

class CarToTypesTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('car_to_types')->insert([
            'car_id' => 1,
            'car_type_id' => 1,
        ]);
        DB::table('car_to_types')->insert([
            'car_id' => 1,
            'car_type_id' => 2,
        ]);
    }
}
