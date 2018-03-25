<?php

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;

class CarsTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('cars')->insert([
            'code' => 'A152RUS',
            'name' => 'Lada #1',
        ]);
    }
}
