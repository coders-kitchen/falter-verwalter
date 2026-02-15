<?php

namespace Database\Seeders;

use App\Models\Family;
use App\Models\LifeForm;
use App\Models\Plant;
use App\Models\User;
use Illuminate\Database\Seeder;

class PlantFamilyAndPlantDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (!app()->environment(['local', 'testing'])) {
            return;
        }

        $adminId = User::query()->where('role', 'admin')->value('id')
            ?? User::query()->value('id');

        if (!$adminId) {
            return;
        }

        $lifeFormIds = $this->ensureLifeForms((int) $adminId);

        // Keep runtime dataset around ~100 entries for manageable test data.
        $plants = array_slice($this->plantDataset(), 0, 100);
        $familyIds = $this->ensurePlantFamilies((int) $adminId, $plants);

        foreach ($plants as $index => $item) {
            $seed = crc32($item['scientific_name']);

            $bloomStart = 3 + ($seed % 4); // Mar-Jun
            $bloomEnd = min(10, $bloomStart + 2 + ($seed % 3));

            $hFrom = 10 + ($seed % 70);
            $hTo = $hFrom + 20 + ($seed % 120);

            $light = 3 + ($seed % 7);
            $temperature = 2 + ($seed % 8);
            $continentality = 2 + ($seed % 8);
            $reaction = 3 + ($seed % 7);
            $moisture = 2 + ($seed % 8);
            $moistureVariation = 1 + ($seed % 7);
            $nitrogen = 2 + ($seed % 8);
            $salt = ($seed % 5 === 0) ? null : (1 + ($seed % 9));

            $heavyLevels = Plant::HEAVY_METAL_RESISTANCE_LEVELS;
            $heavy = $heavyLevels[$seed % count($heavyLevels)];

            Plant::updateOrCreate(
                ['scientific_name' => $item['scientific_name']],
                [
                    'user_id' => (int) $adminId,
                    'life_form_id' => $lifeFormIds[$item['life_form']],
                    'family_id' => $familyIds[$item['family']],
                    'name' => $item['name'],
                    'family_genus' => explode(' ', $item['scientific_name'])[0] ?? null,
                    'light_number' => $light,
                    'salt_number' => $salt,
                    'temperature_number' => $temperature,
                    'continentality_number' => $continentality,
                    'reaction_number' => $reaction,
                    'moisture_number' => $moisture,
                    'moisture_variation' => $moistureVariation,
                    'nitrogen_number' => $nitrogen,
                    'bloom_start_month' => $bloomStart,
                    'bloom_end_month' => $bloomEnd,
                    'bloom_color' => $this->pickBloomColor($seed),
                    'plant_height_cm_from' => $hFrom,
                    'plant_height_cm_until' => $hTo,
                    'lifespan' => $this->pickLifespan($seed),
                    'location' => 'Mitteleuropa',
                    'is_native' => $item['is_native'],
                    'is_invasive' => $item['is_invasive'],
                    'threat_status' => null,
                    'heavy_metal_resistance' => $heavy,
                    'persistence_organs' => null,
                ]
            );
        }
    }

    private function ensureLifeForms(int $adminId): array
    {
        $needed = ['Baum', 'Strauch', 'Kraut', 'Gras'];
        $ids = [];

        foreach ($needed as $name) {
            $lifeForm = LifeForm::firstOrCreate(
                ['name' => $name],
                [
                    'user_id' => $adminId,
                    'description' => $name,
                    'examples' => [],
                ]
            );
            $ids[$name] = $lifeForm->id;
        }

        return $ids;
    }

    private function ensurePlantFamilies(int $adminId, array $plants): array
    {
        $names = collect($plants)
            ->pluck('family')
            ->unique()
            ->sort()
            ->values();

        $ids = [];
        foreach ($names as $familyName) {
            $family = Family::firstOrCreate(
                [
                    'name' => $familyName,
                    'subfamily' => null,
                    'genus' => null,
                    'tribe' => null,
                    'type' => 'plant',
                ],
                [
                    'user_id' => $adminId,
                    'description' => 'Automatisch erzeugte Pflanzenfamilie für Demo-/Testdaten.',
                ]
            );

            $ids[$familyName] = $family->id;
        }

        return $ids;
    }

    private function pickBloomColor(int $seed): string
    {
        $colors = ['weiß', 'gelb', 'rosa', 'blau', 'violett', 'rot', 'grünlich'];
        return $colors[$seed % count($colors)];
    }

    private function pickLifespan(int $seed): string
    {
        $lifespans = ['annual', 'biennial', 'perennial'];
        return $lifespans[$seed % count($lifespans)];
    }

    private function plantDataset(): array
    {
        return [
            ['name' => 'Wiesen-Schafgarbe', 'scientific_name' => 'Achillea millefolium', 'family' => 'Asteraceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Gänseblümchen', 'scientific_name' => 'Bellis perennis', 'family' => 'Asteraceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Wiesen-Margerite', 'scientific_name' => 'Leucanthemum vulgare', 'family' => 'Asteraceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Gewöhnlicher Löwenzahn', 'scientific_name' => 'Taraxacum officinale', 'family' => 'Asteraceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Acker-Kratzdistel', 'scientific_name' => 'Cirsium arvense', 'family' => 'Asteraceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Wiesen-Flockenblume', 'scientific_name' => 'Centaurea jacea', 'family' => 'Asteraceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Gewöhnlicher Beifuß', 'scientific_name' => 'Artemisia vulgaris', 'family' => 'Asteraceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Huflattich', 'scientific_name' => 'Tussilago farfara', 'family' => 'Asteraceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Echte Goldrute', 'scientific_name' => 'Solidago virgaurea', 'family' => 'Asteraceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Kleines Habichtskraut', 'scientific_name' => 'Pilosella officinarum', 'family' => 'Asteraceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Wasserdost', 'scientific_name' => 'Eupatorium cannabinum', 'family' => 'Asteraceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Jakobs-Greiskraut', 'scientific_name' => 'Jacobaea vulgaris', 'family' => 'Asteraceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],

            ['name' => 'Rotklee', 'scientific_name' => 'Trifolium pratense', 'family' => 'Fabaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Weißklee', 'scientific_name' => 'Trifolium repens', 'family' => 'Fabaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Gewöhnlicher Hornklee', 'scientific_name' => 'Lotus corniculatus', 'family' => 'Fabaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Vogel-Wicke', 'scientific_name' => 'Vicia cracca', 'family' => 'Fabaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Luzerne', 'scientific_name' => 'Medicago sativa', 'family' => 'Fabaceae', 'life_form' => 'Kraut', 'is_native' => false, 'is_invasive' => false],
            ['name' => 'Esparsette', 'scientific_name' => 'Onobrychis viciifolia', 'family' => 'Fabaceae', 'life_form' => 'Kraut', 'is_native' => false, 'is_invasive' => false],
            ['name' => 'Wiesen-Platterbse', 'scientific_name' => 'Lathyrus pratensis', 'family' => 'Fabaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Echter Steinklee', 'scientific_name' => 'Melilotus officinalis', 'family' => 'Fabaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],

            ['name' => 'Wiesen-Salbei', 'scientific_name' => 'Salvia pratensis', 'family' => 'Lamiaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Sand-Thymian', 'scientific_name' => 'Thymus serpyllum', 'family' => 'Lamiaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Dost', 'scientific_name' => 'Origanum vulgare', 'family' => 'Lamiaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Weiße Taubnessel', 'scientific_name' => 'Lamium album', 'family' => 'Lamiaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Purpurrote Taubnessel', 'scientific_name' => 'Lamium purpureum', 'family' => 'Lamiaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Kriechender Günsel', 'scientific_name' => 'Ajuga reptans', 'family' => 'Lamiaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Wasserminze', 'scientific_name' => 'Mentha aquatica', 'family' => 'Lamiaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Kleine Braunelle', 'scientific_name' => 'Prunella vulgaris', 'family' => 'Lamiaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Heil-Ziest', 'scientific_name' => 'Betonica officinalis', 'family' => 'Lamiaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Katzenminze', 'scientific_name' => 'Nepeta cataria', 'family' => 'Lamiaceae', 'life_form' => 'Kraut', 'is_native' => false, 'is_invasive' => false],

            ['name' => 'Brombeere', 'scientific_name' => 'Rubus fruticosus', 'family' => 'Rosaceae', 'life_form' => 'Strauch', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Himbeere', 'scientific_name' => 'Rubus idaeus', 'family' => 'Rosaceae', 'life_form' => 'Strauch', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Wald-Erdbeere', 'scientific_name' => 'Fragaria vesca', 'family' => 'Rosaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Gewöhnliche Nelkenwurz', 'scientific_name' => 'Geum urbanum', 'family' => 'Rosaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Mädesüß', 'scientific_name' => 'Filipendula ulmaria', 'family' => 'Rosaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Blutwurz', 'scientific_name' => 'Potentilla erecta', 'family' => 'Rosaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Hunds-Rose', 'scientific_name' => 'Rosa canina', 'family' => 'Rosaceae', 'life_form' => 'Strauch', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Eingriffeliger Weißdorn', 'scientific_name' => 'Crataegus monogyna', 'family' => 'Rosaceae', 'life_form' => 'Strauch', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Schlehe', 'scientific_name' => 'Prunus spinosa', 'family' => 'Rosaceae', 'life_form' => 'Strauch', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Holz-Apfel', 'scientific_name' => 'Malus sylvestris', 'family' => 'Rosaceae', 'life_form' => 'Baum', 'is_native' => true, 'is_invasive' => false],

            ['name' => 'Wilde Möhre', 'scientific_name' => 'Daucus carota', 'family' => 'Apiaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Wiesen-Kerbel', 'scientific_name' => 'Anthriscus sylvestris', 'family' => 'Apiaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Wiesen-Bärenklau', 'scientific_name' => 'Heracleum sphondylium', 'family' => 'Apiaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Wald-Engelwurz', 'scientific_name' => 'Angelica sylvestris', 'family' => 'Apiaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Wiesen-Pastinak', 'scientific_name' => 'Pastinaca sativa', 'family' => 'Apiaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Kleine Bibernelle', 'scientific_name' => 'Pimpinella saxifraga', 'family' => 'Apiaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],

            ['name' => 'Acker-Senf', 'scientific_name' => 'Sinapis arvensis', 'family' => 'Brassicaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Knoblauchsrauke', 'scientific_name' => 'Alliaria petiolata', 'family' => 'Brassicaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Wiesen-Schaumkraut', 'scientific_name' => 'Cardamine pratensis', 'family' => 'Brassicaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Acker-Schmalwand', 'scientific_name' => 'Arabidopsis thaliana', 'family' => 'Brassicaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Raps', 'scientific_name' => 'Brassica napus', 'family' => 'Brassicaceae', 'life_form' => 'Kraut', 'is_native' => false, 'is_invasive' => false],

            ['name' => 'Spitzwegerich', 'scientific_name' => 'Plantago lanceolata', 'family' => 'Plantaginaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Breitwegerich', 'scientific_name' => 'Plantago major', 'family' => 'Plantaginaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Gamander-Ehrenpreis', 'scientific_name' => 'Veronica chamaedrys', 'family' => 'Plantaginaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Roter Fingerhut', 'scientific_name' => 'Digitalis purpurea', 'family' => 'Plantaginaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],

            ['name' => 'Rote Lichtnelke', 'scientific_name' => 'Silene dioica', 'family' => 'Caryophyllaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Kartäuser-Nelke', 'scientific_name' => 'Dianthus carthusianorum', 'family' => 'Caryophyllaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Vogelmiere', 'scientific_name' => 'Stellaria media', 'family' => 'Caryophyllaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Echtes Seifenkraut', 'scientific_name' => 'Saponaria officinalis', 'family' => 'Caryophyllaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Kuckucks-Lichtnelke', 'scientific_name' => 'Silene flos-cuculi', 'family' => 'Caryophyllaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],

            ['name' => 'Scharfer Hahnenfuß', 'scientific_name' => 'Ranunculus acris', 'family' => 'Ranunculaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Sumpf-Dotterblume', 'scientific_name' => 'Caltha palustris', 'family' => 'Ranunculaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Akelei', 'scientific_name' => 'Aquilegia vulgaris', 'family' => 'Ranunculaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],

            ['name' => 'Natternkopf', 'scientific_name' => 'Echium vulgare', 'family' => 'Boraginaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Echtes Lungenkraut', 'scientific_name' => 'Pulmonaria officinalis', 'family' => 'Boraginaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Acker-Vergissmeinnicht', 'scientific_name' => 'Myosotis arvensis', 'family' => 'Boraginaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Beinwell', 'scientific_name' => 'Symphytum officinale', 'family' => 'Boraginaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Borretsch', 'scientific_name' => 'Borago officinalis', 'family' => 'Boraginaceae', 'life_form' => 'Kraut', 'is_native' => false, 'is_invasive' => false],

            ['name' => 'Knaulgras', 'scientific_name' => 'Dactylis glomerata', 'family' => 'Poaceae', 'life_form' => 'Gras', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Rotschwingel', 'scientific_name' => 'Festuca rubra', 'family' => 'Poaceae', 'life_form' => 'Gras', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Wiesen-Rispengras', 'scientific_name' => 'Poa pratensis', 'family' => 'Poaceae', 'life_form' => 'Gras', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Deutsches Weidelgras', 'scientific_name' => 'Lolium perenne', 'family' => 'Poaceae', 'life_form' => 'Gras', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Wiesen-Lieschgras', 'scientific_name' => 'Phleum pratense', 'family' => 'Poaceae', 'life_form' => 'Gras', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Glatthafer', 'scientific_name' => 'Arrhenatherum elatius', 'family' => 'Poaceae', 'life_form' => 'Gras', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Rotes Straußgras', 'scientific_name' => 'Agrostis capillaris', 'family' => 'Poaceae', 'life_form' => 'Gras', 'is_native' => true, 'is_invasive' => false],

            ['name' => 'Wiesen-Sauerampfer', 'scientific_name' => 'Rumex acetosa', 'family' => 'Polygonaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Vogel-Knöterich', 'scientific_name' => 'Polygonum aviculare', 'family' => 'Polygonaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Floh-Knöterich', 'scientific_name' => 'Persicaria maculosa', 'family' => 'Polygonaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],

            ['name' => 'Große Brennnessel', 'scientific_name' => 'Urtica dioica', 'family' => 'Urticaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Kleine Brennnessel', 'scientific_name' => 'Urtica urens', 'family' => 'Urticaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],

            ['name' => 'Zypressen-Wolfsmilch', 'scientific_name' => 'Euphorbia cyparissias', 'family' => 'Euphorbiaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Sonnenwend-Wolfsmilch', 'scientific_name' => 'Euphorbia helioscopia', 'family' => 'Euphorbiaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],

            ['name' => 'Wiesen-Schlüsselblume', 'scientific_name' => 'Primula veris', 'family' => 'Primulaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Gewöhnlicher Gilbweiderich', 'scientific_name' => 'Lysimachia vulgaris', 'family' => 'Primulaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Acker-Gauchheil', 'scientific_name' => 'Lysimachia arvensis', 'family' => 'Primulaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],

            ['name' => 'Klatschmohn', 'scientific_name' => 'Papaver rhoeas', 'family' => 'Papaveraceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Schöllkraut', 'scientific_name' => 'Chelidonium majus', 'family' => 'Papaveraceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],

            ['name' => 'Rundblättrige Glockenblume', 'scientific_name' => 'Campanula rotundifolia', 'family' => 'Campanulaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Wiesen-Glockenblume', 'scientific_name' => 'Campanula patula', 'family' => 'Campanulaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Nesselblättrige Glockenblume', 'scientific_name' => 'Campanula trachelium', 'family' => 'Campanulaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],

            ['name' => 'Wiesen-Storchschnabel', 'scientific_name' => 'Geranium pratense', 'family' => 'Geraniaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Ruprechtskraut', 'scientific_name' => 'Geranium robertianum', 'family' => 'Geraniaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],

            ['name' => 'Wilde Malve', 'scientific_name' => 'Malva sylvestris', 'family' => 'Malvaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Stockrose', 'scientific_name' => 'Alcea rosea', 'family' => 'Malvaceae', 'life_form' => 'Kraut', 'is_native' => false, 'is_invasive' => false],

            ['name' => 'Echtes Johanniskraut', 'scientific_name' => 'Hypericum perforatum', 'family' => 'Hypericaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],

            ['name' => 'Kleiner Klappertopf', 'scientific_name' => 'Rhinanthus minor', 'family' => 'Orobanchaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],

            ['name' => 'Acker-Winde', 'scientific_name' => 'Convolvulus arvensis', 'family' => 'Convolvulaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Zaunwinde', 'scientific_name' => 'Calystegia sepium', 'family' => 'Convolvulaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],

            ['name' => 'Wiesen-Witwenblume', 'scientific_name' => 'Knautia arvensis', 'family' => 'Caprifoliaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Echter Baldrian', 'scientific_name' => 'Valeriana officinalis', 'family' => 'Caprifoliaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Wald-Geißblatt', 'scientific_name' => 'Lonicera periclymenum', 'family' => 'Caprifoliaceae', 'life_form' => 'Strauch', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Tauben-Skabiose', 'scientific_name' => 'Scabiosa columbaria', 'family' => 'Caprifoliaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],

            ['name' => 'Wildes Stiefmütterchen', 'scientific_name' => 'Viola tricolor', 'family' => 'Violaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Duft-Veilchen', 'scientific_name' => 'Viola odorata', 'family' => 'Violaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],

            ['name' => 'Schmalblättriges Weidenröschen', 'scientific_name' => 'Chamaenerion angustifolium', 'family' => 'Onagraceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Gemeine Nachtkerze', 'scientific_name' => 'Oenothera biennis', 'family' => 'Onagraceae', 'life_form' => 'Kraut', 'is_native' => false, 'is_invasive' => false],

            ['name' => 'Besenheide', 'scientific_name' => 'Calluna vulgaris', 'family' => 'Ericaceae', 'life_form' => 'Strauch', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Heidelbeere', 'scientific_name' => 'Vaccinium myrtillus', 'family' => 'Ericaceae', 'life_form' => 'Strauch', 'is_native' => true, 'is_invasive' => false],

            ['name' => 'Kultur-Lein', 'scientific_name' => 'Linum usitatissimum', 'family' => 'Linaceae', 'life_form' => 'Kraut', 'is_native' => false, 'is_invasive' => false],
            ['name' => 'Purgier-Lein', 'scientific_name' => 'Linum catharticum', 'family' => 'Linaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],

            ['name' => 'Wiesen-Schaumkraut', 'scientific_name' => 'Cardamine amara', 'family' => 'Brassicaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Echtes Labkraut', 'scientific_name' => 'Galium verum', 'family' => 'Rubiaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Waldmeister', 'scientific_name' => 'Galium odoratum', 'family' => 'Rubiaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Acker-Witwenblume', 'scientific_name' => 'Knautia dipsacifolia', 'family' => 'Caprifoliaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Gemeiner Odermennig', 'scientific_name' => 'Agrimonia eupatoria', 'family' => 'Rosaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Wiesen-Bocksbart', 'scientific_name' => 'Tragopogon pratensis', 'family' => 'Asteraceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Echte Kamille', 'scientific_name' => 'Matricaria chamomilla', 'family' => 'Asteraceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Rainfarn', 'scientific_name' => 'Tanacetum vulgare', 'family' => 'Asteraceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Kornblume', 'scientific_name' => 'Centaurea cyanus', 'family' => 'Asteraceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Wegwarte', 'scientific_name' => 'Cichorium intybus', 'family' => 'Asteraceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Wiesen-Pippau', 'scientific_name' => 'Crepis biennis', 'family' => 'Asteraceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Gelbe Skabiose', 'scientific_name' => 'Scabiosa ochroleuca', 'family' => 'Caprifoliaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Gewöhnliche Schafgarbe', 'scientific_name' => 'Achillea ptarmica', 'family' => 'Asteraceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Gewöhnliche Kriech-Quecke', 'scientific_name' => 'Elymus repens', 'family' => 'Poaceae', 'life_form' => 'Gras', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Rohr-Schwingel', 'scientific_name' => 'Festuca arundinacea', 'family' => 'Poaceae', 'life_form' => 'Gras', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Silbergras', 'scientific_name' => 'Corynephorus canescens', 'family' => 'Poaceae', 'life_form' => 'Gras', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Wiesen-Fuchsschwanz', 'scientific_name' => 'Alopecurus pratensis', 'family' => 'Poaceae', 'life_form' => 'Gras', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Ruchgras', 'scientific_name' => 'Anthoxanthum odoratum', 'family' => 'Poaceae', 'life_form' => 'Gras', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Berg-Ahorn', 'scientific_name' => 'Acer pseudoplatanus', 'family' => 'Sapindaceae', 'life_form' => 'Baum', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Spitz-Ahorn', 'scientific_name' => 'Acer platanoides', 'family' => 'Sapindaceae', 'life_form' => 'Baum', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Sommer-Linde', 'scientific_name' => 'Tilia platyphyllos', 'family' => 'Malvaceae', 'life_form' => 'Baum', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Winter-Linde', 'scientific_name' => 'Tilia cordata', 'family' => 'Malvaceae', 'life_form' => 'Baum', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Stiel-Eiche', 'scientific_name' => 'Quercus robur', 'family' => 'Fagaceae', 'life_form' => 'Baum', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Rot-Buche', 'scientific_name' => 'Fagus sylvatica', 'family' => 'Fagaceae', 'life_form' => 'Baum', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Hasel', 'scientific_name' => 'Corylus avellana', 'family' => 'Betulaceae', 'life_form' => 'Strauch', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Hänge-Birke', 'scientific_name' => 'Betula pendula', 'family' => 'Betulaceae', 'life_form' => 'Baum', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Schwarzerle', 'scientific_name' => 'Alnus glutinosa', 'family' => 'Betulaceae', 'life_form' => 'Baum', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Sal-Weide', 'scientific_name' => 'Salix caprea', 'family' => 'Salicaceae', 'life_form' => 'Baum', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Zitter-Pappel', 'scientific_name' => 'Populus tremula', 'family' => 'Salicaceae', 'life_form' => 'Baum', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Schwarzer Holunder', 'scientific_name' => 'Sambucus nigra', 'family' => 'Adoxaceae', 'life_form' => 'Strauch', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Kornelkirsche', 'scientific_name' => 'Cornus mas', 'family' => 'Cornaceae', 'life_form' => 'Strauch', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Roter Hartriegel', 'scientific_name' => 'Cornus sanguinea', 'family' => 'Cornaceae', 'life_form' => 'Strauch', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Wolliger Schneeball', 'scientific_name' => 'Viburnum lantana', 'family' => 'Adoxaceae', 'life_form' => 'Strauch', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Gewöhnlicher Schneeball', 'scientific_name' => 'Viburnum opulus', 'family' => 'Adoxaceae', 'life_form' => 'Strauch', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Wiesen-Sauerklee', 'scientific_name' => 'Oxalis acetosella', 'family' => 'Oxalidaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Gewöhnlicher Rainkohl', 'scientific_name' => 'Lapsana communis', 'family' => 'Asteraceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Acker-Winde', 'scientific_name' => 'Fallopia convolvulus', 'family' => 'Polygonaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Gemeiner Frauenmantel', 'scientific_name' => 'Alchemilla vulgaris', 'family' => 'Rosaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Sumpf-Schafgarbe', 'scientific_name' => 'Achillea salicifolia', 'family' => 'Asteraceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
            ['name' => 'Sumpf-Storchschnabel', 'scientific_name' => 'Geranium palustre', 'family' => 'Geraniaceae', 'life_form' => 'Kraut', 'is_native' => true, 'is_invasive' => false],
        ];
    }
}
