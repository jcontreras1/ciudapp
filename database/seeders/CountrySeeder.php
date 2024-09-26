<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach(City::all() as $city){
            if(!count($city->institutions)){
                $city->delete();
            }
        }
        foreach(Province::all() as $province){
            if(!count($province->cities)){
                $province->delete();
            }
        }
        foreach(Country::all() as $country){
            if(!count($country->provinces)){
                $country->delete();
            }
        }

        DB::unprepared('INSERT into country (id, name) values (1, "Argentina")');
        $this->call(ProvinceSeeder::class);
    }
}
