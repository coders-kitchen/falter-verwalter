<?php

use App\Livewire\PlantManager;
use App\Models\Family;
use App\Models\Genus;
use App\Models\LifeForm;
use App\Models\Plant;
use App\Models\Species;
use App\Models\SpeciesPlant;
use App\Models\Subfamily;
use App\Models\User;
use Livewire\Livewire;

test('plant manager shows usage count and blocks deletion for used plants', function () {
    $user = User::factory()->create();

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
        'lifespan' => 'perennial',
    ]);

    SpeciesPlant::create([
        'species_id' => $species->id,
        'plant_id' => $plant->id,
        'is_nectar' => true,
        'is_larval_host' => false,
        'adult_preference' => SpeciesPlant::PREFERENCE_PRIMARY,
        'larval_preference' => null,
    ]);

    $this->actingAs($user);

    Livewire::test(PlantManager::class)
        ->assertSee('Wiesen-Flockenblume')
        ->assertSee('1')
        ->call('delete', $plant->id);

    $this->assertDatabaseHas('plants', [
        'id' => $plant->id,
        'name' => 'Wiesen-Flockenblume',
    ]);
});
