<?php

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;

class TariffRelationsTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('tariff_relations')->insert([
            'user_type_id' => 1,
            'car_type_id' => 1,
            'stage_id' => 1,
            'tariff_id' => 1,
        ]);
        DB::table('tariff_relations')->insert([
            'user_type_id' => 1,
            'car_type_id' => 1,
            'stage_id' => 1,
            'tariff_id' => 2,
        ]);
        DB::table('tariff_relations')->insert([
            'user_type_id' => 1,
            'car_type_id' => 1,
            'stage_id' => 1,
            'tariff_id' => 3,
        ]);
        DB::table('tariff_relations')->insert([
            'user_type_id' => 1,
            'car_type_id' => 1,
            'stage_id' => 1,
            'tariff_id' => 4,
        ]);
    }
}
