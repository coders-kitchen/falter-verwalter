<?php

namespace Tests\Unit\Models;

use App\Models\DistributionArea;
use App\Models\Species;
use App\Models\ThreatCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpeciesTest extends TestCase
{
    use RefreshDatabase;

    public function test_species_distribution_areas_relationship(): void
    {
        $species = Species::factory()->create();
        $user = User::factory()->create();
        $threatCategory = ThreatCategory::create([
            'code' => 'VU',
            'label' => 'Vulnerable',
            'description' => 'Gefaehrdet',
            'rank' => 1,
            'color_code' => '#ff0000',
            'user_id' => $user->id,
        ]);
        $area1 = DistributionArea::create([
            'name' => 'Area 1',
            'description' => 'First area',
            'user_id' => $user->id,
        ]);
        $area2 = DistributionArea::create([
            'name' => 'Area 2',
            'description' => 'Second area',
            'user_id' => $user->id,
        ]);

        $species->distributionAreas()->attach($area1->id, [
            'status' => 'heimisch',
            'threat_category_id' => $threatCategory->id,
            'user_id' => $user->id,
        ]);
        $species->distributionAreas()->attach($area2->id, [
            'status' => 'neobiotisch',
            'threat_category_id' => $threatCategory->id,
            'user_id' => $user->id,
        ]);

        $speciesWithAreas = Species::with('distributionAreas')->find($species->id);

        $this->assertCount(2, $speciesWithAreas->distributionAreas);
        $this->assertTrue($speciesWithAreas->distributionAreas->contains($area1));
        $this->assertTrue($speciesWithAreas->distributionAreas->contains($area2));
    }

    public function test_distribution_areas_pivot_contains_threat_and_status(): void
    {
        $species = Species::factory()->create();
        $user = User::factory()->create();
        $threatCategory = ThreatCategory::create([
            'code' => 'VU',
            'label' => 'Vulnerable',
            'description' => 'Gefaehrdet',
            'rank' => 1,
            'color_code' => '#ff0000',
            'user_id' => $user->id,
        ]);
        $area = DistributionArea::create([
            'name' => 'Area 1',
            'description' => 'First area',
            'user_id' => $user->id,
        ]);

        $species->distributionAreas()->attach($area->id, [
            'status' => 'heimisch',
            'threat_category_id' => $threatCategory->id,
            'user_id' => $user->id,
        ]);

        $speciesWithAreas = Species::with('distributionAreas')->find($species->id);
        $areaFromSpecies = $speciesWithAreas->distributionAreas->first();

        $this->assertEquals('heimisch', $areaFromSpecies->pivot->status);
        $this->assertEquals($threatCategory->id, $areaFromSpecies->pivot->threat_category_id);
    }

    public function test_sync_distribution_areas_with_pivot_data(): void
    {
        $species = Species::factory()->create();
        $user = User::factory()->create();
        $threatCategory = ThreatCategory::create([
            'code' => 'VU',
            'label' => 'Vulnerable',
            'description' => 'Gefaehrdet',
            'rank' => 1,
            'color_code' => '#ff0000',
            'user_id' => $user->id,
        ]);
        $area1 = DistributionArea::create(['name' => 'Area 1', 'description' => null, 'user_id' => $user->id]);
        $area2 = DistributionArea::create(['name' => 'Area 2', 'description' => null, 'user_id' => $user->id]);
        $area3 = DistributionArea::create(['name' => 'Area 3', 'description' => null, 'user_id' => $user->id]);

        $species->distributionAreas()->sync([
            $area1->id => ['status' => 'heimisch', 'threat_category_id' => $threatCategory->id, 'user_id' => $user->id],
            $area2->id => ['status' => 'neobiotisch', 'threat_category_id' => $threatCategory->id, 'user_id' => $user->id],
        ]);

        $species->distributionAreas()->sync([
            $area2->id => ['status' => 'heimisch', 'threat_category_id' => $threatCategory->id, 'user_id' => $user->id],
            $area3->id => ['status' => 'ausgestorben', 'threat_category_id' => $threatCategory->id, 'user_id' => $user->id],
        ]);

        $speciesWithAreas = Species::with('distributionAreas')->find($species->id);

        $this->assertCount(2, $speciesWithAreas->distributionAreas);
        $this->assertFalse($speciesWithAreas->distributionAreas->contains($area1));
        $this->assertTrue($speciesWithAreas->distributionAreas->contains($area2));
        $this->assertTrue($speciesWithAreas->distributionAreas->contains($area3));
    }
}
