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
                'code' => 'mitteleuropa',
                'description' => 'Deutschland, Österreich, Tschechien, Schweiz',
            ],
            [
                'name' => 'Nordeuropa',
                'code' => 'nordeuropa',
                'description' => 'Skandinavien, Baltikum',
            ],
            [
                'name' => 'Südeuropa',
                'code' => 'suedeuropa',
                'description' => 'Mittelmeerraum',
            ],
            [
                'name' => 'Westeuropa',
                'code' => 'westeuropa',
                'description' => 'Frankreich, Großbritannien, Benelux',
            ],
            [
                'name' => 'Osteuropa',
                'code' => 'osteuropa',
                'description' => 'Polen, Ukraine, Russland',
            ],
        ];

        foreach ($areas as $area) {
            DistributionArea::create(array_merge($area, ['user_id' => 1]));
        }
    }
}
