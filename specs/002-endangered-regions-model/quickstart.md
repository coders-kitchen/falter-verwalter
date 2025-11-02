# Quick Start Guide: Endangered Regions Model Implementation

**Feature**: 002-endangered-regions-model
**Target Audience**: Developers implementing this feature
**Date**: 2025-11-02

---

## Overview

This guide provides a quick reference for implementing the endangered species regional model refactoring. For detailed specifications, see [spec.md](./spec.md) and [data-model.md](./data-model.md).

**Key Change**: Separate where a species occurs from how endangered it is in each region.

---

## Architecture Summary

### Before (Wrong)
```
Species → endangered_region_id → EndangeredRegion
                    ↓
        (Single endangered region per species)
```

### After (Correct)
```
Species ←→ Region (via species_region pivot)
        ↓
        conservation_status ('nicht_gefährdet' | 'gefährdet')
```

---

## Implementation Checklist

### Phase 1: Database
- [ ] Create migration: `create_regions_table`
  - Migrate from endangered_regions
  - Fields: id, code, name, description, timestamps

- [ ] Create migration: `create_species_region_table`
  - Fields: id, species_id, region_id, conservation_status (enum), timestamps
  - Unique constraint on (species_id, region_id)
  - Foreign keys with CASCADE DELETE

- [ ] Create migration: `migrate_endangered_data`
  - Copy endangered_regions → regions
  - Copy species_endangered_region → species_region (set all to 'nicht_gefährdet')
  - Archive old tables

- [ ] Run migrations and verify data integrity
  - Count records match before/after
  - All foreign keys valid

### Phase 2: Models
- [ ] Create `Region` model
  - Namespace: `App\Models\Region`
  - Relationships: `species()` BelongsToMany

- [ ] Create `SpeciesRegion` model (pivot model)
  - Namespace: `App\Models\SpeciesRegion`
  - Relationships: `species()`, `region()`

- [ ] Update `Species` model
  - Add `regions()` BelongsToMany relationship
  - Remove references to `endangeredRegions()` (or deprecate)
  - Update `getters` that use conservation status

### Phase 3: Admin UI
- [ ] Update `SpeciesManager` Livewire component
  - Add "Geographic Distribution" section
  - Multi-select regions from checkbox/select
  - Add "Conservation Status" section
  - For each region: dropdown with rating options
  - Form validation: at least 1 region required

- [ ] Update `species-manager.blade.php` view
  - Visually separate region selection from rating
  - Show current regions with their ratings
  - Add/remove region buttons
  - Change rating dropdown per region

### Phase 4: Data Migration Script
- [ ] Create migration with data transfer logic
  - Insert safe default ('nicht_gefährdet') for all existing mappings
  - Verify no data loss
  - Create rollback logic

- [ ] Test migration on staging
  - Verify counts match
  - Check queries still work
  - Confirm indexes exist

### Phase 5: Public Features
- [ ] Update `SpeciesBrowser` component
  - Update endangered_region filter to use new regions/conservation_status
  - Update queries to join species_region instead of species_endangered_region

- [ ] Update `RegionalDistributionMap` component
  - Update count queries for endangered species
  - Join species_region to count by conservation_status

- [ ] Update `SpeciesDetail` view
  - Show regions where species occurs
  - Show conservation status per region
  - Separate "where it lives" from "how endangered it is"

### Phase 6: Testing
- [ ] Unit tests for models
  - Species.regions() relationship
  - Region.species() relationship
  - Pivot conservation_status

- [ ] Feature tests for admin
  - Add region to species
  - Change conservation status
  - Delete region from species

- [ ] Integration tests for public
  - Species browser filters
  - Regional map counts
  - Detail page display

### Phase 7: Cleanup & Documentation
- [ ] Remove references to old EndangeredRegion model (or archive)
- [ ] Update route model binding if applicable
- [ ] Archive old tables (don't delete yet)
- [ ] Update admin documentation
- [ ] Update user guides

---

## Code Examples

### Creating Models

#### Region Model
```php
// app/Models/Region.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Region extends Model
{
    protected $fillable = ['code', 'name', 'description'];

    public function species(): BelongsToMany
    {
        return $this->belongsToMany(Species::class, 'species_region')
            ->withPivot('conservation_status')
            ->withTimestamps();
    }
}
```

#### SpeciesRegion Pivot Model
```php
// app/Models/SpeciesRegion.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SpeciesRegion extends Pivot
{
    public $incrementing = true;
    protected $table = 'species_region';
    protected $fillable = ['species_id', 'region_id', 'conservation_status'];

    const CONSERVATION_STATUS = [
        'nicht_gefährdet' => 'Not Endangered',
        'gefährdet' => 'Endangered',
    ];
}
```

#### Species Model Update
```php
// Add to Species model
public function regions(): BelongsToMany
{
    return $this->belongsToMany(
        Region::class,
        'species_region',
        'species_id',
        'region_id'
    )
    ->using(SpeciesRegion::class)
    ->withPivot('conservation_status')
    ->withTimestamps();
}

// Get endangered regions only
public function endangeredRegions()
{
    return $this->regions()
        ->wherePivot('conservation_status', 'gefährdet');
}
```

### Common Operations

#### Add Region to Species
```php
$species = Species::find(1);
$regionId = 5;

// Attach with default 'nicht_gefährdet'
$species->regions()->attach($regionId);

// Or with specific status
$species->regions()->attach($regionId, [
    'conservation_status' => 'gefährdet'
]);
```

#### Update Conservation Status
```php
$species->regions()->updateExistingPivot(
    $regionId,
    ['conservation_status' => 'gefährdet']
);
```

#### Get Endangered Species in Region
```php
$region = Region::find(1);
$endangeredSpecies = $region->species()
    ->wherePivot('conservation_status', 'gefährdet')
    ->get();
```

#### Count Species per Region
```php
Region::withCount([
    'species as total_count',
    'species as endangered_count' => function($q) {
        $q->wherePivot('conservation_status', 'gefährdet');
    }
])->get();
```

### Livewire Component Example

```php
// app/Livewire/SpeciesManager.php - Updated

public function updatedSelectedRegionIds()
{
    // When regions change, validate
    foreach ($this->selectedRegionIds as $regionId) {
        if (!isset($this->form['conservation_status'][$regionId])) {
            $this->form['conservation_status'][$regionId] = 'nicht_gefährdet';
        }
    }
}

public function save()
{
    $this->validate();

    $regionIds = [];
    foreach ($this->selectedRegionIds as $regionId) {
        $regionIds[$regionId] = [
            'conservation_status' => $this->form['conservation_status'][$regionId]
        ];
    }

    $species = Species::find($this->speciesId);
    $species->regions()->sync($regionIds);
}
```

---

## Database Migrations Template

```php
// database/migrations/YYYY_MM_DD_create_regions_table.php
Schema::create('regions', function (Blueprint $table) {
    $table->id();
    $table->string('code', 10)->unique();
    $table->string('name', 255);
    $table->text('description')->nullable();
    $table->timestamps();

    $table->index('code');
    $table->index('name');
});

// database/migrations/YYYY_MM_DD_create_species_region_table.php
Schema::create('species_region', function (Blueprint $table) {
    $table->id();
    $table->foreignId('species_id')->constrained()->cascadeOnDelete();
    $table->foreignId('region_id')->constrained('regions')->cascadeOnDelete();
    $table->enum('conservation_status', ['nicht_gefährdet', 'gefährdet'])
        ->default('nicht_gefährdet');
    $table->timestamps();

    $table->unique(['species_id', 'region_id']);
    $table->index(['species_id', 'conservation_status']);
    $table->index('region_id');
});

// database/migrations/YYYY_MM_DD_migrate_endangered_data.php
// Copy from endangered_regions to regions
// Copy from species_endangered_region to species_region
```

---

## Testing Examples

```php
// tests/Feature/SpeciesRegionManagementTest.php
use Tests\TestCase;
use App\Models\Species;
use App\Models\Region;

class SpeciesRegionManagementTest extends TestCase
{
    /** @test */
    public function can_add_region_to_species()
    {
        $species = Species::factory()->create();
        $region = Region::factory()->create();

        $species->regions()->attach($region->id);

        $this->assertDatabaseHas('species_region', [
            'species_id' => $species->id,
            'region_id' => $region->id,
            'conservation_status' => 'nicht_gefährdet',
        ]);
    }

    /** @test */
    public function can_update_conservation_status()
    {
        $species = Species::factory()->create();
        $region = Region::factory()->create();
        $species->regions()->attach($region->id);

        $species->regions()->updateExistingPivot(
            $region->id,
            ['conservation_status' => 'gefährdet']
        );

        $this->assertDatabaseHas('species_region', [
            'species_id' => $species->id,
            'region_id' => $region->id,
            'conservation_status' => 'gefährdet',
        ]);
    }
}
```

---

## Common Pitfalls & Solutions

| Pitfall | Problem | Solution |
|---------|---------|----------|
| Forgetting to use `withPivot()` | Conservation status not loaded | Always use `.withPivot('conservation_status')` |
| Using old EndangeredRegion model | Queries fail or miss data | Update all references to new `regions()` relationship |
| N+1 queries in loop | Performance degrades | Use eager loading: `.with('regions')` |
| Null conservation_status | Data inconsistency | Use default value in migration, check migrations |
| Missing unique constraint | Duplicate region assignments | Add UNIQUE(species_id, region_id) to migration |
| Forgetting CASCADE DELETE | Orphaned records remain | Ensure foreign keys have onDelete('cascade') |

---

## Performance Checklist

- [ ] Indexes created on `(species_id, conservation_status)`
- [ ] Index on `region_id`
- [ ] Eager loading used in queries: `.with('regions')`
- [ ] Pagination applied to large lists
- [ ] Region list cached (since it changes rarely)
- [ ] Query N+1 issues tested and resolved
- [ ] Admin form loads in <1 second
- [ ] Regional map queries execute in <500ms

---

## References

- **Specification**: [spec.md](./spec.md)
- **Data Model Details**: [data-model.md](./data-model.md)
- **Implementation Plan**: [plan.md](./plan.md)
- **Laravel Eloquent Docs**: https://laravel.com/docs/11.x/eloquent-relationships
- **Task Breakdown**: [tasks.md](./tasks.md) (/speckit.tasks output)

---

## Getting Help

If you encounter issues:

1. **Check data-model.md** for detailed entity definitions
2. **Review code examples** above for reference implementations
3. **Run tests** to verify each component works
4. **Check performance** with database analysis tools
5. **Verify migrations** ran successfully: `php artisan migrate:status`

---

**Ready to start implementation?** Run `/speckit.tasks` to generate detailed tasks with dependencies.
