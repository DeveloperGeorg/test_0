<?php

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;

class UserToTypesTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('user_to_types')->insert([
            'user_id' => 1,
            'user_type_id' => 1,
        ]);
    }
}
