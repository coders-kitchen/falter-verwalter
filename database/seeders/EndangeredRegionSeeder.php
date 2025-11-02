<?php

namespace Database\Seeders;

use App\Models\EndangeredRegion;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EndangeredRegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first user (or create one for seeding)
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'System',
                'email' => 'system@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        $regions = [
            ['code' => 'NRW', 'name' => 'Nordrhein-Westfalen'],
            ['code' => 'WB', 'name' => 'Württemberg'],
            ['code' => 'BGL', 'name' => 'Berchtesgaden'],
            ['code' => 'NTRL', 'name' => 'Neutral/Sonstig'],
            ['code' => 'NRBU', 'name' => 'Nordburgund'],
            ['code' => 'WT', 'name' => 'Westthüringen'],
            ['code' => 'WBEL', 'name' => 'Webelbelg'],
            ['code' => 'EI', 'name' => 'Eifel/Siebengebirge'],
            ['code' => 'SSl', 'name' => 'Südschwarzwald'],
        ];

        foreach ($regions as $region) {
            EndangeredRegion::updateOrCreate(
                ['code' => $region['code']],
                [
                    'user_id' => $user->id,
                    'name' => $region['name'],
                    'description' => 'Region where this butterfly species is endangered',
                ]
            );
        }
    }
}
