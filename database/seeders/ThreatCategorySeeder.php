<?php

namespace Database\Seeders;

use App\Models\ThreatCategory;
use Illuminate\Database\Seeder;

class ThreatCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
  [
    "code" => "LC",
    "label" => "Nicht gefährdet",
    "description" => "Die Art ist weit verbreitet und weist stabile oder zunehmende Bestände auf. Es bestehen derzeit keine erkennbaren Gefährdungen.",
    "rank" => 0,
    "color_code" => '#cfcfcf'
  ],
  [
    "code" => "NT",
    "label" => "Vorwarnliste",
    "description" => "Die Art ist aktuell noch nicht gefährdet, zeigt jedoch Bestandsrückgänge oder andere Entwicklungen, die sie künftig gefährden könnten.",
    "rank" => 1,
    "color_code" => '#cfcfcf'
  ],
  [
    "code" => "VU",
    "label" => "Gefährdet",
    "description" => "Die Art ist einem hohen Risiko des Aussterbens in der Natur ausgesetzt, wenn die derzeitigen negativen Entwicklungen anhalten.",
    "rank" => 2,
    "color_code" => '#cfcfcf'
  ],
  [
    "code" => "EN",
    "label" => "Stark gefährdet",
    "description" => "Die Art ist einem sehr hohen Risiko des Aussterbens in der Natur ausgesetzt und weist deutliche Bestandsverluste oder starke Arealverkleinerungen auf.",
    "rank" => 3,
    "color_code" => '#cfcfcf'
  ],
  [
    "code" => "CR",
    "label" => "Vom Aussterben bedroht",
    "description" => "Die Art ist einem extrem hohen Risiko des Aussterbens in der Natur ausgesetzt und steht unmittelbar vor dem vollständigen Verschwinden.",
    "rank" => 4,
    "color_code" => '#cfcfcf'
  ],
  [
    "code" => "EW",
    "label" => "In der Natur ausgestorben",
    "description" => "Die Art existiert nur noch in menschlicher Obhut oder außerhalb ihres ursprünglichen natürlichen Verbreitungsgebiets.",
    "rank" => 5,
    "color_code" => '#cfcfcf'
  ],
  [
    "code" => "EX",
    "label" => "Ausgestorben",
    "description" => "Es besteht kein begründeter Zweifel daran, dass das letzte Individuum der Art gestorben ist.",
    "rank" => 6,
    "color_code" => '#cfcfcf'
  ],
  [
    "code" => "DD",
    "label" => "Unzureichende Datenlage",
    "description" => "Für eine Bewertung des Aussterberisikos liegen nicht genügend Informationen zur Verbreitung oder Bestandsentwicklung der Art vor.",
    "rank" => 7,
    "color_code" => '#cfcfcf'
  ],
  [
    "code" => "NE",
    "label" => "Nicht bewertet",
    "description" => "Die Art wurde bisher noch nicht nach den Kriterien der IUCN bewertet.",
    "rank" => 8,
    "color_code" => '#cfcfcf'
  ]
];

        foreach ($categories as $category) {
            ThreatCategory::create(array_merge($category, ['user_id' => 1]));
        }
    }
}
