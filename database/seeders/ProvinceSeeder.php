<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class ProvinceSeeder extends Seeder
{
    /**
    * Run the database seeds.
    */
    public function run(): void
    {
        
        DB::unprepared("
        INSERT INTO `province` (`id`, `name`, `country_id`) VALUES
            (1, 'Buenos Aires', 1),
            (2, 'Buenos Aires-GBA', 1),
            (3, 'Capital Federal', 1),
            (4, 'Catamarca', 1),
            (5, 'Chaco', 1),
            (6, 'Chubut', 1),
            (7, 'Córdoba', 1),
            (8, 'Corrientes', 1),
            (9, 'Entre Ríos', 1),
            (10, 'Formosa', 1),
            (11, 'Jujuy', 1),
            (12, 'La Pampa', 1),
            (13, 'La Rioja', 1),
            (14, 'Mendoza', 1),
            (15, 'Misiones', 1),
            (16, 'Neuquén', 1),
            (17, 'Río Negro', 1),
            (18, 'Salta', 1),
            (19, 'San Juan', 1),
            (20, 'San Luis', 1),
            (21, 'Santa Cruz', 1),
            (22, 'Santa Fe', 1),
            (23, 'Santiago del Estero', 1),
            (24, 'Tierra del Fuego', 1),
            (25, 'Tucumán', 1);
        ");

        $this->call(CitySeeder::class);
    }
}
