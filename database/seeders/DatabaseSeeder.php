<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Category::firstOrCreate([
            'name' => 'Municipio',
            'icon' => '<i class="fas fa-building"></i>',
        ]);
        Category::firstOrCreate([
            'name' => 'Servicoop',
            'icon' => '<i class="fas fa-tree"></i>',
        ]);
        Category::firstOrCreate([
            'name' => 'Policia',
            'icon' => '<i class="fas fa-shield-alt"></i>',
        ]);


        Subcategory::firstOrCreate([
            'name' => 'Obras',
            'category_id' => 1,
            'icon' => '<i class="fas fa-tools"></i>',
        ]);

        Subcategory::firstOrCreate([
            'name' => 'Limpieza',
            'category_id' => 1,
            'icon' => '<i class="fas fa-broom"></i>',
        ]);

        Subcategory::firstOrCreate([
           'name' => 'Tránsito',
            'category_id' => 1,
            'icon' => '<i class="fas fa-car"></i>',
        ]);

        Subcategory::firstOrCreate([
            'name' => 'Alumbrado',
            'category_id' => 2,
            'icon' => '<i class="fas fa-lightbulb"></i>',
        ]);
        
        Subcategory::firstOrCreate([
            'name' => 'Cloacas',
            'category_id' => 2,
            'icon' => '<i class="fas fa-toilet"></i>',
        ]);

        Subcategory::firstOrCreate([
            'name' => 'Energía',
            'category_id' => 2,
            'icon' => '<i class="fas fa-plug"></i>',
        ]);

        Subcategory::firstOrCreate([
            'name' => 'Emergencias',
            'category_id' => 3,
            'icon' => '<i class="fas fa-ambulance"></i>',          
        ]);

        Subcategory::firstOrCreate([
            'name' => 'Denuncias',
            'category_id' => 3,
            'icon' => '<i class="fas fa-exclamation-triangle"></i>',
        ]);


    }
}
