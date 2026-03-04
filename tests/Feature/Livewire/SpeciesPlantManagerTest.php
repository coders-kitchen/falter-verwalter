<?php

use App\Livewire\SpeciesPlantManager;
use App\Models\Family;
use App\Models\Genus;
use App\Models\LifeForm;
use App\Models\Plant;
use App\Models\Species;
use App\Models\SpeciesGenus;
use App\Models\SpeciesPlant;
use App\Models\Subfamily;
use App\Models\User;
use Livewire\Livewire;

function createSpeciesPlantManagerFixture(): array
{
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
    ]);

    return compact('user', 'species', 'genus', 'plant');
}

test('species plant manager saves phagy levels for plant and genus assignments', function () {
    $fixture = createSpeciesPlantManagerFixture();

    $this->actingAs($fixture['user']);

    Livewire::test(SpeciesPlantManager::class, ['speciesId' => $fixture['species']->id])
        ->call('openCreateModal')
        ->set('form.is_nectar', true)
        ->set('form.adult_preference', SpeciesPlant::PREFERENCE_SECONDARY)
        ->set('form.adult_phagy_level', SpeciesPlant::PHAGY_MONOPHAG)
        ->set('addSelectedPlantIds', [$fixture['plant']->id])
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('species_plant', [
        'species_id' => $fixture['species']->id,
        'plant_id' => $fixture['plant']->id,
        'adult_preference' => SpeciesPlant::PREFERENCE_SECONDARY,
        'adult_phagy_level' => SpeciesPlant::PHAGY_MONOPHAG,
        'larval_phagy_level' => null,
    ]);

    Livewire::test(SpeciesPlantManager::class, ['speciesId' => $fixture['species']->id])
        ->call('openCreateModal')
        ->set('assignmentType', 'genus')
        ->set('form.is_larval_host', true)
        ->set('form.larval_preference', SpeciesPlant::PREFERENCE_PRIMARY)
        ->set('form.larval_phagy_level', SpeciesPlant::PHAGY_OLIGOPHAG)
        ->set('addSelectedGenusIds', [$fixture['genus']->id])
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('species_genus', [
        'species_id' => $fixture['species']->id,
        'genus_id' => $fixture['genus']->id,
        'larval_preference' => SpeciesPlant::PREFERENCE_PRIMARY,
        'larval_phagy_level' => SpeciesPlant::PHAGY_OLIGOPHAG,
        'adult_phagy_level' => null,
    ]);
});

test('species plant manager page renders phagy labels for mixed assignments', function () {
    $fixture = createSpeciesPlantManagerFixture();

    $this->actingAs($fixture['user']);

    SpeciesPlant::create([
        'species_id' => $fixture['species']->id,
        'plant_id' => $fixture['plant']->id,
        'is_nectar' => true,
        'is_larval_host' => false,
        'adult_preference' => SpeciesPlant::PREFERENCE_PRIMARY,
        'larval_preference' => null,
        'adult_phagy_level' => SpeciesPlant::PHAGY_MONOPHAG,
        'larval_phagy_level' => null,
    ]);

    SpeciesGenus::create([
        'species_id' => $fixture['species']->id,
        'genus_id' => $fixture['genus']->id,
        'is_nectar' => false,
        'is_larval_host' => true,
        'adult_preference' => null,
        'larval_preference' => SpeciesPlant::PREFERENCE_PRIMARY,
        'adult_phagy_level' => null,
        'larval_phagy_level' => SpeciesPlant::PHAGY_POLYPHAG,
    ]);

    $response = $this->get(route('admin.speciesPlants.index', $fixture['species']));

    $response->assertOk();
    $response->assertSee('Wiesen-Flockenblume');
    $response->assertSee('Centaurea (sp.)');
    $response->assertSee('Monophag');
    $response->assertSee('Polyphag');
});
