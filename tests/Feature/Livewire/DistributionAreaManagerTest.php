<?php

use App\Livewire\DistributionAreaManager;
use App\Models\DistributionArea;
use App\Models\DistributionAreaLevel;
use App\Models\Family;
use App\Models\Species;
use App\Models\User;
use Livewire\Livewire;

test('distribution area manager shows level and blocks deletion when species are assigned', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $level = DistributionAreaLevel::create([
        'name' => 'Detail',
        'code' => 'detail-test',
        'sort_order' => 20,
        'map_role' => 'detail',
    ]);

    $family = Family::create([
        'user_id' => $user->id,
        'name' => 'Nymphalidae',
        'type' => 'butterfly',
    ]);

    $species = Species::create([
        'user_id' => $user->id,
        'family_id' => $family->id,
        'name' => 'Distelfalter',
        'scientific_name' => 'Vanessa cardui',
        'size_category' => 'M',
    ]);

    $distributionArea = DistributionArea::create([
        'user_id' => $user->id,
        'distribution_area_level_id' => $level->id,
        'name' => 'Bergisches Land',
        'code' => 'bergisches-land',
    ]);

    $species->distributionAreas()->attach($distributionArea->id, [
        'status' => 'heimisch',
        'user_id' => $user->id,
    ]);

    Livewire::test(DistributionAreaManager::class)
        ->assertSee('Bergisches Land')
        ->assertSee('Detail')
        ->assertSee('1')
        ->call('delete', $distributionArea->id);

    $this->assertDatabaseHas('distribution_areas', ['id' => $distributionArea->id]);
});
