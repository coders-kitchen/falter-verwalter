# Data Model: Endangered Species Regional Model

**Feature**: 002-endangered-regions-model
**Date**: 2025-11-02
**Status**: Design Phase

---

## Entity Relationships

### Current Model (Deprecated)
```
Species ←→ EndangeredRegion (Many-to-Many pivot)
       (species_endangered_region table)
```

**Problem**: Conflates "species occurs here" with "species is endangered here"

### Proposed Model (New)
```
Species ←→ Region (Many-to-Many with ConservationStatus)
       ↓
    SpeciesRegion (pivot with conservation_status enum)
```

**Solution**: Separates occurrence (which regions) from conservation status (how endangered)

---

## Entity Definitions

### 1. Region (New)
**Purpose**: Represents geographic regions where species may occur

**Table**: `regions`

| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | BIGINT | PK, AUTO_INCREMENT | |
| code | VARCHAR(10) | UNIQUE, NOT NULL | e.g., "NRW", "NRBU", "Bayern" |
| name | VARCHAR(255) | NOT NULL | e.g., "Nord Rhein Westfalen" |
| description | TEXT | NULLABLE | Additional region information |
| created_at | TIMESTAMP | DEFAULT NOW() | |
| updated_at | TIMESTAMP | DEFAULT NOW() ON UPDATE NOW() | |

**Validation Rules**:
- `code`: Required, unique, max 10 characters, no spaces
- `name`: Required, max 255 characters
- `description`: Optional, max 1000 characters

**Indexes**:
- PRIMARY KEY on `id`
- UNIQUE KEY on `code`
- KEY on `name` (for sorting/searching)

**Relationships**:
- `species()`: BelongsToMany → Species (through species_region)

---

### 2. SpeciesRegion (New - Pivot Model)
**Purpose**: Maps species to regions with conservation status

**Table**: `species_region`

| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | BIGINT | PK, AUTO_INCREMENT | |
| species_id | BIGINT | FK, NOT NULL | References species.id |
| region_id | BIGINT | FK, NOT NULL | References regions.id |
| conservation_status | ENUM | NOT NULL | 'nicht_gefährdet' OR 'gefährdet' |
| created_at | TIMESTAMP | DEFAULT NOW() | |
| updated_at | TIMESTAMP | DEFAULT NOW() ON UPDATE NOW() | |

**Validation Rules**:
- `species_id`: Required, must exist in species table
- `region_id`: Required, must exist in regions table
- `conservation_status`: Required, one of allowed enum values
- Combination of species_id + region_id must be unique (no duplicates)

**Indexes**:
- PRIMARY KEY on `id`
- UNIQUE KEY on `(species_id, region_id)`
- KEY on `species_id` (for queries filtering by species)
- KEY on `region_id` (for queries filtering by region)
- KEY on `conservation_status` (for filtering endangered species)

**Foreign Keys**:
- `species_id` → species.id (CASCADE DELETE)
- `region_id` → regions.id (CASCADE DELETE)

**Relationships**:
- `species()`: BelongsTo → Species
- `region()`: BelongsTo → Region

**Default Value**:
- `conservation_status` defaults to 'nicht_gefährdet' when created

---

### 3. Species (Updated)
**Changes**: Add new relationship for regions

**New Relationship**:
```php
public function regions(): BelongsToMany
{
    return $this->belongsToMany(
        Region::class,
        'species_region',
        'species_id',
        'region_id'
    )
    ->withPivot('conservation_status')
    ->withTimestamps();
}
```

**Usage Examples**:
```php
// Get all regions where species occurs
$species->regions()->get();

// Get all regions where species is endangered
$species->regions()
    ->wherePivot('conservation_status', 'gefährdet')
    ->get();

// Get conservation status for a specific region
$status = $species->regions()
    ->where('region_id', $regionId)
    ->first()
    ->pivot
    ->conservation_status;

// Add region with default rating (nicht_gefährdet)
$species->regions()->attach($regionId);

// Update conservation status
$species->regions()
    ->updateExistingPivot($regionId, [
        'conservation_status' => 'gefährdet'
    ]);
```

---

### 4. EndangeredRegion (Deprecated)
**Status**: Archived after migration

**Migration Path**:
1. Data from `species_endangered_region` → `species_region` with default 'nicht_gefährdet'
2. Table `endangered_regions` → backup table `endangered_regions_archive`
3. Remove from production code
4. Keep code references for backward compatibility queries (if needed)

---

## Data Migration Strategy

### Pre-Migration
1. **Backup**: Create backup of current endangered_regions data
2. **Audit**: Verify all current data is consistent
3. **Test**: Run migration on staging database first

### Migration Steps
1. Create `regions` table (copy from `endangered_regions`)
2. Create `species_region` pivot table
3. Migrate data from `species_endangered_region` to `species_region`:
   - Insert all current species-region mappings
   - Set `conservation_status` to 'nicht_gefährdet' (default/assumed)
   - Preserve timestamps
4. Update foreign key constraints
5. Add database indexes

### Post-Migration
1. **Validation**: Count records before/after (must match)
2. **Testing**: Run feature tests to verify queries work
3. **Rollback Plan**: Keep old tables for 2 weeks, then archive

### SQL Migration Script Structure
```sql
-- Create regions table from endangered_regions
INSERT INTO regions (code, name, description, created_at, updated_at)
SELECT code, name, description, created_at, updated_at
FROM endangered_regions;

-- Create species_region from old species_endangered_region
INSERT INTO species_region (species_id, region_id, conservation_status, created_at, updated_at)
SELECT species_id, endangered_region_id, 'nicht_gefährdet', created_at, updated_at
FROM species_endangered_region;

-- Rename old table for backup
ALTER TABLE endangered_regions RENAME TO endangered_regions_archive;
ALTER TABLE species_endangered_region RENAME TO species_endangered_region_archive;
```

---

## State Transitions & Constraints

### Species Region State Machine
```
[Region Added]
    ↓
[Conservation Status: nicht_gefährdet] ← Default
    ↓
[Status Changed] → gefährdet
    ↓
    ├─→ [Status Changed] → (future ratings)
    └─→ [Region Deleted] → (removed from system)
```

### Validation Rules
1. **Species must have >= 1 region selected**
   - Public API should return error if no regions assigned
   - Admin UI should warn if species becomes regionless

2. **All regions must have conservation_status assigned**
   - Not nullable in database
   - Default value ensures this is enforced
   - UI must not allow unrated regions

3. **No duplicate region assignments**
   - UNIQUE constraint on (species_id, region_id)
   - Database enforces at persistence level

4. **Cascading deletes**
   - Delete region → deletes all species_region pairings → deletes ratings
   - Delete species → deletes all species_region pairings and ratings

---

## Query Patterns

### Common Queries

#### Get species occurring in a region
```php
$region = Region::find($regionId);
$species = $region->species()->get();
```

#### Get endangered species in a region
```php
$region = Region::find($regionId);
$endangeredSpecies = $region->species()
    ->wherePivot('conservation_status', 'gefährdet')
    ->get();
```

#### Count endangered species per region
```php
Region::withCount([
    'species as endangered_count' => function($query) {
        $query->wherePivot('conservation_status', 'gefährdet');
    }
])->get();
```

#### Get species with region details
```php
Species::with(['regions' => function($query) {
    $query->selectRaw('regions.*, species_region.conservation_status');
}])->get();
```

#### Filter species by region AND conservation status
```php
Species::whereHas('regions', function($query) use ($regionId) {
    $query->where('region_id', $regionId)
          ->wherePivot('conservation_status', 'gefährdet');
})->get();
```

---

## Performance Considerations

### Indexing Strategy
- Composite index on `(species_id, conservation_status)` for endangered filters
- Index on `region_id` for region-based queries
- Index on `conservation_status` for aggregations

### Query Optimization
- Use eager loading: `.with('regions')` to avoid N+1
- Use `wherePivot()` to filter on pivot table efficiently
- Use database indexes for sort operations
- Cache region list (changes infrequently)

### Potential Bottlenecks & Solutions
| Bottleneck | Cause | Solution |
|------------|-------|----------|
| Slow conservation status filtering | No index on pivot column | Add index on conservation_status |
| N+1 queries loading regions | Missing eager loading | Use `.with('regions')` |
| Region list load on every page | Repeated queries | Cache regions in Redis |
| Large species lists with regions | All relationships loaded | Paginate and use `select()` |

---

## Testing Strategy

### Unit Tests
1. **Model relationships**
   - `Species.regions()` returns correct regions
   - `Region.species()` returns correct species
   - Pivot data (conservation_status) loads correctly

2. **Validation**
   - Cannot create species_region without species or region
   - Conservation_status defaults to 'nicht_gefährdet'
   - Unique constraint prevents duplicate assignments

3. **Cascading**
   - Deleting region deletes all species_region records
   - Deleting species deletes all species_region records

### Integration Tests
1. **Admin operations**
   - Add region to species (with default rating)
   - Change conservation status
   - Remove region from species
   - Bulk operations

2. **Public features**
   - Species browser filters by region
   - Regional map displays correct counts
   - Filters combine correctly (region AND endangered)

3. **Data migration**
   - Old data migrates correctly
   - No data loss
   - Counts match before/after

### Query Tests
1. Find all species in region
2. Find endangered species by region
3. Count species per region
4. Filter species by multiple regions

---

## Backward Compatibility

### Deprecation Path
1. **Phase 1**: New `species_region` table exists alongside old `species_endangered_region`
2. **Phase 2**: Code uses new table; old table available for reference
3. **Phase 3**: Archive old table; remove from codebase

### Breaking Changes
- Queries on `species_endangered_region` must be updated to `species_region`
- `EndangeredRegion` model queries must use `Region` model
- API responses must reflect new field names (if applicable)

### Compatibility Helpers (Optional)
```php
// Temporary helper to get old-style data
public function getEndangeredRegionsCompat() {
    return $this->regions()
        ->wherePivot('conservation_status', 'gefährdet')
        ->get();
}
```

---

## Documentation References

- **Specification**: [spec.md](./spec.md)
- **Implementation Plan**: [plan.md](./plan.md)
- **Quick Start Guide**: [quickstart.md](./quickstart.md)
- **Task Breakdown**: [tasks.md](./tasks.md) (generated by /speckit.tasks)
