<?php

use App\Livewire\DistributionAreaManager;
use App\Livewire\HabitatManager;
use App\Livewire\LifeFormManager;
use App\Livewire\TagManager;
use App\Livewire\ThreatCategoryManager;
use App\Models\DistributionArea;
use App\Models\DistributionAreaLevel;
use App\Models\Family;
use App\Models\Habitat;
use App\Models\LifeForm;
use App\Models\Plant;
use App\Models\Species;
use App\Models\Tag;
use App\Models\ThreatCategory;
use App\Models\User;
use Livewire\Livewire;

function createMasterDataFixture(): array
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
        'name' => 'Wiesen-Flockenblume',
        'scientific_name' => 'Centaurea jacea',
    ]);

    return compact('user', 'species', 'plant', 'lifeForm');
}

test('habitat manager shows split usage counts and blocks deletion when used', function () {
    $fixture = createMasterDataFixture();
    $this->actingAs($fixture['user']);

    $habitat = Habitat::create([
        'user_id' => $fixture['user']->id,
        'name' => 'Trockenrasen',
        'level' => 0,
    ]);

    $fixture['species']->habitats()->attach($habitat->id);
    $fixture['plant']->habitats()->attach($habitat->id);

    Livewire::test(HabitatManager::class)
        ->assertSee('Trockenrasen')
        ->assertSee('2')
        ->assertSeeHtml('title="1 Arten, 1 Pflanzen"')
        ->call('delete', $habitat->id);

    $this->assertDatabaseHas('habitats', ['id' => $habitat->id]);
});

test('tag manager blocks deletion when species use the tag', function () {
    $fixture = createMasterDataFixture();
    $this->actingAs($fixture['user']);

    $tag = Tag::create([
        'name' => 'Waldrand',
        'slug' => 'waldrand',
        'is_active' => true,
    ]);

    $fixture['species']->tags()->attach($tag->id);

    Livewire::test(TagManager::class)
        ->assertSee('Waldrand')
        ->assertSee('1')
        ->call('delete', $tag->id);

    $this->assertDatabaseHas('tags', ['id' => $tag->id]);
});

test('distribution area manager blocks deletion when species are assigned', function () {
    $fixture = createMasterDataFixture();
    $this->actingAs($fixture['user']);

    $level = DistributionAreaLevel::create([
        'name' => 'Detail',
        'code' => 'detail-test',
        'sort_order' => 20,
        'map_role' => 'detail',
    ]);

    $distributionArea = DistributionArea::create([
        'user_id' => $fixture['user']->id,
        'distribution_area_level_id' => $level->id,
        'name' => 'Bergisches Land',
        'code' => 'bergisches-land',
    ]);

    $fixture['species']->distributionAreas()->attach($distributionArea->id, [
        'status' => 'heimisch',
        'user_id' => $fixture['user']->id,
    ]);

    Livewire::test(DistributionAreaManager::class)
        ->assertSee('Bergisches Land')
        ->assertSee('Detail')
        ->assertSee('1')
        ->call('delete', $distributionArea->id);

    $this->assertDatabaseHas('distribution_areas', ['id' => $distributionArea->id]);
});

test('life form manager blocks deletion when plants use the life form', function () {
    $fixture = createMasterDataFixture();
    $this->actingAs($fixture['user']);

    Livewire::test(LifeFormManager::class)
        ->assertSee('Staude')
        ->assertSee('1')
        ->call('delete', $fixture['lifeForm']->id);

    $this->assertDatabaseHas('life_forms', ['id' => $fixture['lifeForm']->id]);
});

test('threat category manager shows combined usage and blocks deletion', function () {
    $fixture = createMasterDataFixture();
    $this->actingAs($fixture['user']);

    $level = DistributionAreaLevel::create([
        'name' => 'Detail',
        'code' => 'detail-threat-test',
        'sort_order' => 20,
        'map_role' => 'detail',
    ]);

    $distributionArea = DistributionArea::create([
        'user_id' => $fixture['user']->id,
        'distribution_area_level_id' => $level->id,
        'name' => 'Rheinland',
        'code' => 'rheinland',
    ]);

    $threatCategory = ThreatCategory::create([
        'user_id' => $fixture['user']->id,
        'code' => 'VU',
        'label' => 'Gefaehrdet',
        'description' => 'Gefaehrdet',
        'rank' => 1,
        'color_code' => '#ff0000',
    ]);

    $fixture['plant']->update([
        'threat_category_id' => $threatCategory->id,
    ]);

    $fixture['species']->distributionAreas()->attach($distributionArea->id, [
        'status' => 'heimisch',
        'threat_category_id' => $threatCategory->id,
        'user_id' => $fixture['user']->id,
    ]);

    Livewire::test(ThreatCategoryManager::class)
        ->assertSee('VU')
        ->assertSee('2')
        ->assertSeeHtml('title="1 Arten, 1 Pflanzen"')
        ->call('delete', $threatCategory->id);

    $this->assertDatabaseHas('threat_categories', ['id' => $threatCategory->id]);
});
