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
            'code' => 'DARK_MODE',
            'description' => 'Enable dark mode',
        ]);

        Preference::firstOrCreate([
            'code' => 'NOTIFICATION_COMMENTS',
            'description' => 'Receive notifications for comments',
        ]);

        Preference::firstOrCreate([
            'code' => 'NOTIFICATION_LIKES',
            'description' => 'Receive notifications for likes',
        ]);

        Preference::firstOrCreate([
            'code' => 'NOTIFICATION_COMMENT_LIKES',
            'description' => 'Receive notifications for comment likes',
        ]);
        
        Preference::firstOrCreate([
            'code' => 'PUBLIC_POSTS_BY_DEFAULT',
            'description' => 'Set posts to public by default',
        ]);
        
        Preference::firstOrCreate([
            'code' => 'NOTIFICATION_ON_REGIONS',
            'description' => 'Receive notifications from posts on regions',
        ]);
        
    }
}
