<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeds the 16 German federal states (Bundesländer) as regions
     */
    public function run(): void
    {
        $regions = [
            ['code' => 'BW', 'name' => 'Baden-Württemberg', 'description' => 'Southwestern Germany'],
            ['code' => 'BY', 'name' => 'Bayern', 'description' => 'Bavaria - Southeastern Germany'],
            ['code' => 'BE', 'name' => 'Berlin', 'description' => 'Capital city'],
            ['code' => 'BB', 'name' => 'Brandenburg', 'description' => 'Around Berlin'],
            ['code' => 'HB', 'name' => 'Bremen', 'description' => 'Northwestern Germany'],
            ['code' => 'HH', 'name' => 'Hamburg', 'description' => 'Northern Germany'],
            ['code' => 'HE', 'name' => 'Hessen', 'description' => 'Central Germany'],
            ['code' => 'MV', 'name' => 'Mecklenburg-Vorpommern', 'description' => 'Northeast Germany'],
            ['code' => 'NI', 'name' => 'Niedersachsen', 'description' => 'Lower Saxony - Northern Germany'],
            ['code' => 'NW', 'name' => 'Nordrhein-Westfalen', 'description' => 'North Rhine-Westphalia - Western Germany'],
            ['code' => 'RP', 'name' => 'Rheinland-Pfalz', 'description' => 'Rhineland-Palatinate - Western Germany'],
            ['code' => 'SL', 'name' => 'Saarland', 'description' => 'Southwestern Germany'],
            ['code' => 'SN', 'name' => 'Sachsen', 'description' => 'Saxony - Eastern Germany'],
            ['code' => 'ST', 'name' => 'Sachsen-Anhalt', 'description' => 'Saxony-Anhalt - Eastern Germany'],
            ['code' => 'SH', 'name' => 'Schleswig-Holstein', 'description' => 'Northernmost state'],
            ['code' => 'TH', 'name' => 'Thüringen', 'description' => 'Thuringia - Eastern Germany'],
        ];

        foreach ($regions as $region) {
            Region::updateOrCreate(
                ['code' => $region['code']],
                [
                    'name' => $region['name'],
                    'description' => $region['description'] ?? null,
                ]
            );
        }
    }
}
