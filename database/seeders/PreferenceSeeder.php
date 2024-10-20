<?php

namespace Database\Seeders;

use App\Models\Preference;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PreferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Preference::firstOrCreate([
            'code' => 'NOTIFICATION_COMMENTS',
            'description' => 'Recibir notificaciones de comentarios sobre mis publicaciones',
        ]);

        Preference::firstOrCreate([
            'code' => 'NOTIFICATION_LIKES',
            'description' => 'Recibir notificaciones por gente que se suma a mis publicaciones',
        ]);

        Preference::firstOrCreate([
            'code' => 'NOTIFICATION_COMMENT_LIKES',
            'description' => 'Recibir notiricaciones por gente que le da like a mis comentarios',
        ]);
        
        Preference::firstOrCreate([
            'code' => 'PUBLIC_POSTS_BY_DEFAULT',
            'description' => 'Todos mis posteos son públicos',
        ]);
        
        Preference::firstOrCreate([
            'code' => 'NOTIFICATION_ON_REGIONS',
            'description' => 'Recibir notificaciones acerca de nuevos posts en alguna de mis regiones',
        ]);
        
    }
}
