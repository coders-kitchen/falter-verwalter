<?php

namespace Database\Seeders;

use App\Models\DistributionAreaLevel;
use Illuminate\Database\Seeder;

class DistributionAreaLevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            [
                'name' => 'Hintergrund',
                'code' => 'background',
                'sort_order' => 10,
                'map_role' => DistributionAreaLevel::MAP_ROLE_BACKGROUND,
                'description' => 'Grobere Flaechen fuer Hintergrund-Layer, z. B. Bundeslaender.',
            ],
            [
                'name' => 'Detail',
                'code' => 'detail',
                'sort_order' => 20,
                'map_role' => DistributionAreaLevel::MAP_ROLE_DETAIL,
                'description' => 'Feinere Flaechen fuer Detail-Layer, z. B. Naturräume oder Teilregionen.',
            ],
        ];

        foreach ($levels as $level) {
            DistributionAreaLevel::query()->updateOrCreate(
                ['code' => $level['code']],
                $level,
            );
        }
    }
}
