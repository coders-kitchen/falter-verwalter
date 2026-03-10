<?php

use App\Livewire\Public\PlantButterflyFinder;
use App\Models\Family;
use App\Models\Habitat;
use App\Models\LifeForm;
use App\Models\Plant;
use App\Models\User;
use Livewire\Livewire;

test('public plant finder filters plants by selected habitats', function () {
    $user = User::factory()->create();

    $plantFamily = Family::create([
        'user_id' => $user->id,
        'name' => 'Asteraceae',
        'type' => 'plant',
    ]);

    $lifeForm = LifeForm::create([
        'user_id' => $user->id,
        'name' => 'Staude',
    ]);

    $dryHabitat = Habitat::create([
        'user_id' => $user->id,
        'name' => 'Trockenrasen',
        'level' => 0,
    ]);

    $wetHabitat = Habitat::create([
        'user_id' => $user->id,
        'name' => 'Feuchtwiese',
        'level' => 0,
    ]);

    $dryPlant = Plant::create([
        'user_id' => $user->id,
        'life_form_id' => $lifeForm->id,
        'family_id' => $plantFamily->id,
        'name' => 'Wiesen-Salbei',
        'scientific_name' => 'Salvia pratensis',
    ]);
    $dryPlant->habitats()->attach($dryHabitat->id);

    $wetPlant = Plant::create([
        'user_id' => $user->id,
        'life_form_id' => $lifeForm->id,
        'family_id' => $plantFamily->id,
        'name' => 'Sumpf-Schafgarbe',
        'scientific_name' => 'Achillea ptarmica',
    ]);
    $wetPlant->habitats()->attach($wetHabitat->id);

    Livewire::test(PlantButterflyFinder::class)
        ->assertSee('Wiesen-Salbei')
        ->assertSee('Sumpf-Schafgarbe')
        ->set('filterHabitatIds', [$dryHabitat->id])
        ->assertSee('Wiesen-Salbei')
        ->assertDontSee('Sumpf-Schafgarbe');
});
