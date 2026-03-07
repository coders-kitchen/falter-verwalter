<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            [
                'name' => 'Mineralien saugend',
                'description' => 'Adulte nehmen Mineralstoffe z.B. an feuchten Bodenstellen auf.',
                'is_active' => true,
            ],
            [
                'name' => 'Baumsaftsauger',
                'description' => 'Adulte nutzen austretenden Baumsaft als Nahrungsquelle.',
                'is_active' => true,
            ],
            [
                'name' => 'Salbei-Nutzung',
                'description' => 'Hinweis auf eine relevante Nutzung von Salbei im Kontext der Art.',
                'is_active' => true,
            ],
        ];

        foreach ($tags as $tag) {
            Tag::updateOrCreate(
                ['slug' => Str::slug($tag['name'])],
                $tag
            );
        }
    }
}
