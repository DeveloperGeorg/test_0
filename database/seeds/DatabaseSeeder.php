<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $this->call(StagesTable::class);
         $this->call(CarsTable::class);
         $this->call(CarToTypesTable::class);
         $this->call(CarTypesTable::class);
         $this->call(PriceLimitationRelationsTable::class);
         $this->call(PriceLimitationsTable::class);
         $this->call(PriceLimitTypesTable::class);
         $this->call(PriceTypesTable::class);
         $this->call(TariffRelationsTable::class);
         $this->call(TariffsTable::class);
         $this->call(UserTypesTable::class);
         $this->call(UserToTypesTable::class);
    }
}
