<?php

namespace Database\Seeders;

use App\Models\LifeForm;
use Illuminate\Database\Seeder;

class LifeFormSeeder extends Seeder
{
    public function run(): void
    {
        $lifeForms = [
            [
                'name' => 'Baum',
                'description' => 'Holzige Pflanze mit mehrjährigem Stamm',
                'examples' => ['Buche', 'Eiche', 'Fichte'],
            ],
            [
                'name' => 'Strauch',
                'description' => 'Mehrjährige Pflanze mit mehreren Trieben',
                'examples' => ['Hasel', 'Weißdorn', 'Besenginster'],
            ],
            [
                'name' => 'Kraut',
                'description' => 'Nicht verholzte Pflanze',
                'examples' => ['Klee', 'Brennnessel', 'Kamille'],
            ],
            [
                'name' => 'Gras',
                'description' => 'Gräser und Grasartige',
                'examples' => ['Weidelgras', 'Quecke', 'Blausegge'],
            ],
            [
                'name' => 'Farn',
                'description' => 'Farnpflanzen',
                'examples' => ['Adlerfarn', 'Frauenfarn'],
            ],
        ];

        foreach ($lifeForms as $form) {
            LifeForm::create(array_merge($form, ['user_id' => 1]));
        }
    }
}
