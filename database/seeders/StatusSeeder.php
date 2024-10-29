<?php

namespace Database\Seeders;

use App\Models\IncidentStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        IncidentStatus::firstOrCreate([
            'description' => 'Iniciado',
            'code' => 'open',
            'icon' => 'fa fa-exclamation-circle',
            'color' => '#FFA500'
        ]);

        IncidentStatus::firstOrCreate([
            'description' => 'En progreso',
            'code' => 'progress',
            'icon' => 'fa fa-spinner',
            'color' => '#FFD700'
        ]);

        IncidentStatus::firstOrCreate([
            'description' => 'Resuelto',
            'code' => 'resolved',
            'icon' => 'fa fa-check-circle',
            'color' => '#32CD32'
        ]);

        IncidentStatus::firstOrCreate([
            'description' => 'Cerrado',
            'code' => 'closed',
            'icon' => 'fa fa-times-circle',
            'color' => '#FF0000'
        ]);

        IncidentStatus::firstOrCreate([
            'description' => 'Reabierto',
            'code' => 'reopened',
            'icon' => 'fa fa-exclamation-circle',
            'color' => '#FFA500'
        ]);

        IncidentStatus::firstOrCreate([
            'description' => 'Pendiente',
            'code' => 'pending',
            'icon' => 'fa fa-exclamation-circle',
            'color' => '#FFA500'
        ]);

        IncidentStatus::firstOrCreate([
            'description' => 'Rechazado',
            'code' => 'rejected',
            'icon' => 'fa fa-times-circle',
            'color' => '#FF0000'
        ]);
    }
}
