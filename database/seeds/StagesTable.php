<?php

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;

class StagesTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('stages')->insert([
            'name' => 'Бронирование',
            'code' => 'reservation',
        ]);
        DB::table('stages')->insert([
            'name' => 'Осмотр',
            'code' => 'inspection',
        ]);
        DB::table('stages')->insert([
            'name' => 'Поездка',
            'code' => 'trip',
        ]);
        DB::table('stages')->insert([
            'name' => 'Парковка (Ожидание)',
            'code' => 'waiting',
        ]);
    }
}
