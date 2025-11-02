<?php

namespace Tests\Unit\Models;

use App\Models\Region;
use App\Models\Species;
use App\Models\SpeciesRegion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpeciesRegionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that SpeciesRegion pivot model can be created.
     */
    public function test_species_region_can_be_created(): void
    {
        $species = Species::factory()->create();
        $region = Region::factory()->create();

        $speciesRegion = SpeciesRegion::create([
            'species_id' => $species->id,
            'region_id' => $region->id,
            'conservation_status' => 'gefährdet',
        ]);

        $this->assertNotNull($speciesRegion->id);
        $this->assertEquals('gefährdet', $speciesRegion->conservation_status);
    }

    /**
     * Test that default conservation_status is set correctly.
     */
    public function test_conservation_status_defaults_to_nicht_gefaehrdet(): void
    {
        $species = Species::factory()->create();
        $region = Region::factory()->create();

        // Create without specifying conservation_status
        $speciesRegion = SpeciesRegion::create([
            'species_id' => $species->id,
            'region_id' => $region->id,
        ]);

        $this->assertEquals('nicht_gefährdet', $speciesRegion->conservation_status);
    }

    /**
     * Test that pivot data is accessible from both sides of the relationship.
     */
    public function test_pivot_data_accessible_from_relationships(): void
    {
        $species = Species::factory()->create();
        $region = Region::factory()->create();

        $species->regions()->attach($region->id, [
            'conservation_status' => 'gefährdet',
        ]);

        // Load and verify from species side
        $speciesWithRegion = Species::with('regions')->find($species->id);
        $this->assertEquals('gefährdet', $speciesWithRegion->regions->first()->pivot->conservation_status);

        // Load and verify from region side
        $regionWithSpecies = Region::with('species')->find($region->id);
        $this->assertEquals('gefährdet', $regionWithSpecies->species->first()->pivot->conservation_status);
    }

    /**
     * Test that SpeciesRegion has correct relationships to Species and Region.
     */
    public function test_species_region_relationships(): void
    {
        $species = Species::factory()->create();
        $region = Region::factory()->create();

        $speciesRegion = SpeciesRegion::create([
            'species_id' => $species->id,
            'region_id' => $region->id,
        ]);

        // Test relationship to species
        $this->assertTrue($speciesRegion->species->is($species));

        // Test relationship to region
        $this->assertTrue($speciesRegion->region->is($region));
    }

    /**
     * Test conservation status constants are defined.
     */
    public function test_conservation_status_constants_defined(): void
    {
        $this->assertArrayHasKey('nicht_gefährdet', SpeciesRegion::CONSERVATION_STATUS);
        $this->assertArrayHasKey('gefährdet', SpeciesRegion::CONSERVATION_STATUS);
        $this->assertEquals('Nicht gefährdet', SpeciesRegion::CONSERVATION_STATUS['nicht_gefährdet']);
        $this->assertEquals('Gefährdet', SpeciesRegion::CONSERVATION_STATUS['gefährdet']);
    }
}
