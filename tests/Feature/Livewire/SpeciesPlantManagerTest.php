<?php

use App\Models\Family;
use App\Models\Genus;
use App\Models\LifeForm;
use App\Models\Plant;
use App\Models\Species;
use App\Models\SpeciesGenus;
use App\Models\SpeciesPlant;
use App\Models\Subfamily;
use App\Models\User;

test('species plant manager page renders mixed plant and genus assignments', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $butterflyFamily = Family::create([
        'user_id' => $user->id,
        'name' => 'Nymphalidae',
        'type' => 'butterfly',
    ]);

    $plantFamily = Family::create([
        'user_id' => $user->id,
        'name' => 'Asteraceae',
        'type' => 'plant',
    ]);

    $subfamily = Subfamily::create([
        'family_id' => $plantFamily->id,
        'name' => 'Asteroideae',
    ]);

    $genus = Genus::create([
        'subfamily_id' => $subfamily->id,
        'name' => 'Centaurea',
    ]);

    $lifeForm = LifeForm::create([
        'user_id' => $user->id,
        'name' => 'Staude',
    ]);

    $species = Species::create([
        'user_id' => $user->id,
        'family_id' => $butterflyFamily->id,
        'name' => 'Distelfalter',
        'scientific_name' => 'Vanessa cardui',
        'size_category' => 'M',
    ]);

    $plant = Plant::create([
        'user_id' => $user->id,
        'life_form_id' => $lifeForm->id,
        'family_id' => $plantFamily->id,
        'genus_id' => $genus->id,
        'name' => 'Wiesen-Flockenblume',
        'scientific_name' => 'Centaurea jacea',
        'bloom_start_month' => 6,
        'bloom_end_month' => 9,
        'plant_height_cm_from' => 30,
        'plant_height_cm_until' => 80,
    ]);

    SpeciesPlant::create([
        'species_id' => $species->id,
        'plant_id' => $plant->id,
        'is_nectar' => true,
        'is_larval_host' => false,
        'adult_preference' => SpeciesPlant::PREFERENCE_PRIMARY,
        'larval_preference' => null,
    ]);

    SpeciesGenus::create([
        'species_id' => $species->id,
        'genus_id' => $genus->id,
        'is_nectar' => false,
        'is_larval_host' => true,
        'adult_preference' => null,
        'larval_preference' => SpeciesPlant::PREFERENCE_PRIMARY,
    ]);

    $response = $this->get(route('admin.speciesPlants.index', $species));

    $response->assertOk();
    $response->assertSee('Wiesen-Flockenblume');
    $response->assertSee('Centaurea (sp.)');
});
