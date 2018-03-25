<?php

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;

class UserTypesTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('user_types')->insert([
            'code' => 'default',
            'name' => 'default',
        ]);
    }
}
