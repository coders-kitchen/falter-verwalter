<?php

namespace Tests\Unit\Models;

use App\Models\Region;
use App\Models\Species;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpeciesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that Species.regions() returns all regions for the species.
     */
    public function test_species_regions_relationship(): void
    {
        $species = Species::factory()->create();
        $region1 = Region::factory()->create();
        $region2 = Region::factory()->create();

        $species->regions()->attach($region1->id);
        $species->regions()->attach($region2->id);

        $speciesWithRegions = Species::with('regions')->find($species->id);

        $this->assertCount(2, $speciesWithRegions->regions);
        $this->assertTrue($speciesWithRegions->regions->contains($region1));
        $this->assertTrue($speciesWithRegions->regions->contains($region2));
    }

    /**
     * Test that Species.endangeredRegionsList() returns only endangered regions.
     */
    public function test_endangered_regions_list_filters_by_status(): void
    {
        $species = Species::factory()->create();
        $endangeredRegion = Region::factory()->create();
        $safeRegion = Region::factory()->create();

        $species->regions()->attach($endangeredRegion->id, [
            'conservation_status' => 'gefährdet',
        ]);
        $species->regions()->attach($safeRegion->id, [
            'conservation_status' => 'nicht_gefährdet',
        ]);

        $endangeredRegions = $species->endangeredRegionsList()->get();

        $this->assertCount(1, $endangeredRegions);
        $this->assertTrue($endangeredRegions->contains($endangeredRegion));
        $this->assertFalse($endangeredRegions->contains($safeRegion));
    }

    /**
     * Test that conservation_status pivot data is loaded correctly.
     */
    public function test_regions_pivot_conservation_status(): void
    {
        $species = Species::factory()->create();
        $region = Region::factory()->create();

        $species->regions()->attach($region->id, [
            'conservation_status' => 'gefährdet',
        ]);

        $speciesWithRegions = Species::with('regions')->find($species->id);
        $regionFromSpecies = $speciesWithRegions->regions->first();

        $this->assertEquals('gefährdet', $regionFromSpecies->pivot->conservation_status);
    }

    /**
     * Test that default conservation_status is set when attaching regions.
     */
    public function test_attaching_region_defaults_to_nicht_gefaehrdet(): void
    {
        $species = Species::factory()->create();
        $region = Region::factory()->create();

        // Attach without specifying conservation_status
        $species->regions()->attach($region->id);

        $speciesWithRegions = Species::with('regions')->find($species->id);
        $regionFromSpecies = $speciesWithRegions->regions->first();

        $this->assertEquals('nicht_gefährdet', $regionFromSpecies->pivot->conservation_status);
    }

    /**
     * Test that conservation_status can be updated via updateExistingPivot.
     */
    public function test_update_existing_pivot_conservation_status(): void
    {
        $species = Species::factory()->create();
        $region = Region::factory()->create();

        $species->regions()->attach($region->id);

        // Update conservation status
        $species->regions()->updateExistingPivot($region->id, [
            'conservation_status' => 'gefährdet',
        ]);

        $speciesWithRegions = Species::with('regions')->find($species->id);
        $regionFromSpecies = $speciesWithRegions->regions->first();

        $this->assertEquals('gefährdet', $regionFromSpecies->pivot->conservation_status);
    }

    /**
     * Test that regions can be synced with pivot data.
     */
    public function test_sync_regions_with_pivot_data(): void
    {
        $species = Species::factory()->create();
        $region1 = Region::factory()->create();
        $region2 = Region::factory()->create();
        $region3 = Region::factory()->create();

        // Initial sync with data
        $species->regions()->sync([
            $region1->id => ['conservation_status' => 'gefährdet'],
            $region2->id => ['conservation_status' => 'nicht_gefährdet'],
        ]);

        // Verify initial state
        $this->assertCount(2, $species->regions);

        // Sync again with different data
        $species->regions()->sync([
            $region2->id => ['conservation_status' => 'gefährdet'],
            $region3->id => ['conservation_status' => 'nicht_gefährdet'],
        ]);

        $speciesWithRegions = Species::with('regions')->find($species->id);

        $this->assertCount(2, $speciesWithRegions->regions);
        $this->assertFalse($speciesWithRegions->regions->contains($region1));
        $this->assertTrue($speciesWithRegions->regions->contains($region2));
        $this->assertTrue($speciesWithRegions->regions->contains($region3));

        // Verify status is correct
        $region2FromSpecies = $speciesWithRegions->regions->where('id', $region2->id)->first();
        $this->assertEquals('gefährdet', $region2FromSpecies->pivot->conservation_status);
    }

    /**
     * Test that deleting a region cascades to species_region.
     */
    public function test_deleting_region_cascades_to_species_region(): void
    {
        $species = Species::factory()->create();
        $region = Region::factory()->create();

        $species->regions()->attach($region->id);
        $this->assertCount(1, $species->regions);

        // Delete the region
        $region->delete();

        // Verify the species_region entry was deleted
        $this->assertCount(0, $species->regions()->get());
    }

    /**
     * Test that deleting a species cascades to species_region.
     */
    public function test_deleting_species_cascades_to_species_region(): void
    {
        $species = Species::factory()->create();
        $region = Region::factory()->create();

        $species->regions()->attach($region->id);

        // Delete the species
        $species->delete();

        // Verify the species_region entry was deleted
        $this->assertCount(0, $region->species()->get());
    }
}
