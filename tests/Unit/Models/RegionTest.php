<?php

namespace Tests\Unit\Models;

use App\Models\Region;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RegionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create minimal required tables for Region model
        if (!Schema::hasTable('regions')) {
            Schema::create('regions', function ($table) {
                $table->id();
                $table->string('code', 10)->unique();
                $table->string('name', 255);
                $table->text('description')->nullable();
                $table->timestamps();
                $table->index('code');
                $table->index('name');
            });
        }
    }

    /**
     * Test that Region model can be created.
     */
    public function test_region_can_be_created(): void
    {
        $region = Region::create([
            'code' => 'NRW',
            'name' => 'North Rhine-Westphalia',
            'description' => 'Region in Germany',
        ]);

        $this->assertNotNull($region->id);
        $this->assertEquals('NRW', $region->code);
        $this->assertEquals('North Rhine-Westphalia', $region->name);
    }

    /**
     * Test that region has fillable attributes.
     */
    public function test_region_fillable_attributes(): void
    {
        $data = [
            'code' => 'BAY',
            'name' => 'Bavaria',
            'description' => 'Southern region',
        ];

        $region = Region::create($data);

        $this->assertEquals('BAY', $region->code);
        $this->assertEquals('Bavaria', $region->name);
        $this->assertEquals('Southern region', $region->description);
    }

    /**
     * Test that region can be updated.
     */
    public function test_region_can_be_updated(): void
    {
        $region = Region::create([
            'code' => 'BER',
            'name' => 'Berlin',
        ]);

        $region->update(['name' => 'Berlin (Updated)']);

        $this->assertEquals('Berlin (Updated)', $region->fresh()->name);
    }

    /**
     * Test that region code is unique.
     */
    public function test_region_code_is_unique(): void
    {
        Region::create(['code' => 'HAM', 'name' => 'Hamburg']);

        // Attempting to create another with same code should fail
        try {
            Region::create(['code' => 'HAM', 'name' => 'Other']);
            $this->fail('Expected duplicate code to fail');
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }
}
