<?php

namespace Database\Seeders;

use App\Models\DistributionArea;
use Illuminate\Database\Seeder;

class DistributionAreaSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            [
                'name' => 'Mitteleuropa',
                'description' => 'Deutschland, Österreich, Tschechien, Schweiz',
            ],
            [
                'name' => 'Nordeuropa',
                'description' => 'Skandinavien, Baltikum',
            ],
            [
                'name' => 'Südeuropa',
                'description' => 'Mittelmeerraum',
            ],
            [
                'name' => 'Westeuropa',
                'description' => 'Frankreich, Großbritannien, Benelux',
            ],
            [
                'name' => 'Osteuropa',
                'description' => 'Polen, Ukraine, Russland',
            ],
        ];

        foreach ($areas as $area) {
            DistributionArea::create(array_merge($area, ['user_id' => 1]));
        }
    }
}
