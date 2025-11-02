# Implementation Tasks: Endangered Regions Model Refactoring

**Feature**: 002-endangered-regions-model
**Date Generated**: 2025-11-02
**Status**: Ready for Implementation
**Estimated Duration**: 3-4 days

---

## Overview

This task breakdown implements the separation of species geographic distribution from conservation status. Tasks are organized by user story (from spec.md) with dependencies clearly marked.

**Key User Stories** (from spec.md):
- **US1 (P1)**: Recording Species Distribution - admins select regions where species occur
- **US2 (P1)**: Assigning Conservation Ratings - experts assign status per region
- **US3 (P2)**: Viewing Regional Distribution Map - public sees species distribution
- **US4 (P3)**: Future Enhancement - system extensible for new rating levels

---

## Phase 1: Setup & Infrastructure

**Goal**: Prepare project structure and foundational requirements
**Duration**: 2-4 hours
**Independent Test**: Database migrations run successfully, no data loss

### Database Setup

- [ ] T001 Create migration: `create_regions_table` in `database/migrations/` with code, name, description fields; add unique constraint on code and index on name

- [ ] T002 Create migration: `create_species_region_table` in `database/migrations/` with conservation_status enum field ('nicht_gefährdet', 'gefährdet'), unique constraint on (species_id, region_id), foreign keys with CASCADE DELETE

- [ ] T003 Create migration: `migrate_endangered_data` in `database/migrations/` that copies endangered_regions → regions and species_endangered_region → species_region with default 'nicht_gefährdet' status

- [ ] T004 Run migrations with `php artisan migrate` and verify no errors; check table structure matches expectations

- [ ] T005 Create migration rollback test: verify `php artisan migrate:rollback` restores original state without data loss

---

## Phase 2: Foundational Models & Relationships

**Goal**: Create data models and Eloquent relationships
**Duration**: 3-4 hours
**Blocking**: Required for all user stories
**Independent Test**: Relationships load correctly, pivot data accessible, no N+1 queries

### Models

- [ ] T006 [P] Create `Region` model at `app/Models/Region.php` with $fillable properties (code, name, description) and `species()` BelongsToMany relationship

- [ ] T007 [P] Create `SpeciesRegion` pivot model at `app/Models/SpeciesRegion.php` with $fillable, $table = 'species_region', relationships to Species and Region, conservation_status constant

- [ ] T008 Add `regions()` relationship to `Species` model in `app/Models/Species.php` using BelongsToMany with SpeciesRegion, withPivot('conservation_status'), withTimestamps()

- [ ] T009 [P] Create helper method `endangeredRegions()` in `Species` model that returns regions filtered by conservation_status = 'gefährdet'

- [ ] T010 Write unit tests in `tests/Unit/Models/RegionTest.php` to verify Region.species() relationship loads correctly

- [ ] T011 [P] Write unit tests in `tests/Unit/Models/SpeciesRegionTest.php` for default conservation_status and pivot data access

- [ ] T012 Write unit tests in `tests/Unit/Models/SpeciesTest.php` for Species.regions() and endangeredRegions() methods

- [ ] T013 Run tests: `php artisan test tests/Unit/Models/` and verify all pass

---

## Phase 3: User Story 1 - Recording Species Distribution

**Goal**: Admins can select and manage regions where species occur
**User Story**: [US1] Recording Species Distribution (Priority: P1)
**Duration**: 6-8 hours
**Independent Test Criteria**:
- Admin can add 1+ regions to a species
- Selected regions persist and display on edit
- Admin can remove regions
- Can modify region list for existing species
- Form validates at least 1 region selected
- Cascade delete: removing region removes species_region entry

### Admin UI Component

- [ ] T014 [US1] Update `SpeciesManager` component at `app/Livewire/SpeciesManager.php`: add `selected_region_ids` array property to $form

- [ ] T015 [US1] Add `addRegion($regionId)` method to SpeciesManager that appends region to form array and sets default conservation_status

- [ ] T016 [US1] Add `removeRegion($regionId)` method to SpeciesManager that removes region from form and associated conservation_status

- [ ] T017 [US1] Update `openEditModal(Species $species)` in SpeciesManager to load species.regions() and populate form['selected_region_ids']

- [ ] T018 [US1] Update `save()` method in SpeciesManager to sync regions with pivot data: `$species->regions()->sync($regionData)`

- [ ] T019 [US1] Add form validation rule: `form.selected_region_ids` = required|array|min:1, each ID exists:regions,id

- [ ] T020 [US1] Update Blade template `resources/views/livewire/species-manager.blade.php`:
  - Add "Geographic Distribution" section with region checkboxes
  - Display all available regions with current selection state
  - Add "Add Region" / "Remove Region" buttons
  - Visually separate from conservation status section

### Feature Tests

- [ ] T021 [P] [US1] Write feature test in `tests/Feature/SpeciesRegionManagementTest.php`: test_admin_can_add_region_to_species

- [ ] T022 [P] [US1] Write feature test: test_admin_can_remove_region_from_species

- [ ] T023 [P] [US1] Write feature test: test_admin_cannot_save_species_without_regions

- [ ] T024 [US1] Run tests: `php artisan test tests/Feature/SpeciesRegionManagementTest.php` and verify all pass

---

## Phase 4: User Story 2 - Assigning Conservation Ratings

**Goal**: Conservation experts assign endangered ratings per region
**User Story**: [US2] Assigning Conservation Ratings (Priority: P1)
**Duration**: 6-8 hours
**Depends On**: Phase 3 (regions must exist)
**Independent Test Criteria**:
- Each region gets exactly one conservation_status
- Can change status from 'nicht_gefährdet' to 'gefährdet'
- Can change status back from 'gefährdet' to 'nicht_gefährdet'
- Default status is 'nicht_gefährdet'
- Cannot assign rating to region not assigned to species
- Form validates all regions have ratings

### Admin UI Conservation Status

- [ ] T025 [US2] Add `conservation_status` array property to SpeciesManager $form: [region_id => status]

- [ ] T026 [US2] Add `updateConservationStatus($regionId, $status)` method to SpeciesManager

- [ ] T027 [US2] Update `openEditModal()` to load conservation_status: `$form['conservation_status'] = $species->regions()->pluck('conservation_status', 'regions.id')->toArray()`

- [ ] T028 [US2] Update `save()` method to include conservation_status in pivot sync: `['conservation_status' => $status]`

- [ ] T029 [US2] Add form validation: `form.conservation_status.*` = in:nicht_gefährdet,gefährdet

- [ ] T030 [US2] Update Blade template `resources/views/livewire/species-manager.blade.php`:
  - Add "Conservation Status" section below region selection
  - For each selected region: show code and dropdown with rating options
  - Display current status selection
  - Add feedback when status changes

### Feature Tests

- [ ] T031 [P] [US2] Write feature test: test_admin_can_set_conservation_status

- [ ] T032 [P] [US2] Write feature test: test_admin_can_change_conservation_status

- [ ] T033 [P] [US2] Write feature test: test_default_conservation_status_is_not_endangered

- [ ] T034 [P] [US2] Write feature test: test_cannot_save_without_all_statuses

- [ ] T035 [US2] Run tests: `php artisan test tests/Feature/SpeciesRegionManagementTest.php` and verify all pass

---

## Phase 5: User Story 3 - Viewing Regional Distribution Map

**Goal**: Public visitors see correct species distribution and endangerment
**User Story**: [US3] Viewing Regional Distribution Map (Priority: P2)
**Duration**: 5-6 hours
**Depends On**: Phase 2 (models), Phase 3-4 (data exists)
**Independent Test Criteria**:
- Regional map displays all regions
- Color coding reflects endangered species count (not total)
- Count query executes in <500ms
- Species detail shows correct regions
- Species browser filters by region work correctly

### Public Features - Regional Distribution Map

- [ ] T036 [US3] [P] Update `RegionalDistributionMap` component at `app/Livewire/Public/RegionalDistributionMap.php`:
  - Change from `EndangeredRegion` to `Region` model
  - Update `aggregateRegionData()` to query new schema
  - Count endangered species: `.wherePivot('conservation_status', 'gefährdet')`

- [ ] T037 [US3] Update migration query for `aggregateRegionData()`:
  ```php
  $count = $region->species()
      ->wherePivot('conservation_status', 'gefährdet')
      ->when($this->species, fn($q) => $q->where('species_id', $this->species->id))
      ->distinct()
      ->count();
  ```

- [ ] T038 [US3] Test regional map loads: verify counts match endangered species, color coding accurate, page load <1s

### Public Features - Species Browser

- [ ] T039 [US3] [P] Update `SpeciesBrowser` component at `app/Livewire/Public/SpeciesBrowser.php`:
  - Update endangered status filter to use new pivot table
  - Change `.whereHas('endangeredRegions')` to `.whereHas('regions', fn($q) => $q->wherePivot('conservation_status', 'gefährdet'))`

- [ ] T040 [US3] Update region filter in SpeciesBrowser: support filtering by region selection

- [ ] T041 [US3] Update Blade template `resources/views/public/species-browser.blade.php` to display regions correctly

- [ ] T042 [US3] Test species browser: filters work correctly, queries execute efficiently

### Public Features - Species Detail Page

- [ ] T043 [US3] [P] Update `SpeciesDetail` component at `app/Livewire/Public/SpeciesDetail.php`:
  - Change `.load(['endangeredRegions'])` to `.load(['regions'])`
  - Update any conservation_status references

- [ ] T044 [US3] Update Blade template `resources/views/public/species-detail.blade.php`:
  - Show regions where species occurs in distribution section
  - For each region: display code, name, and conservation_status badge
  - Use color coding (red for endangered, green for not endangered)

- [ ] T045 [US3] Test species detail: regions display correctly, conservation_status shown properly

### Integration Tests

- [ ] T046 [US3] Write integration test: test_regional_map_counts_endangered_species_correctly

- [ ] T047 [US3] Write integration test: test_species_browser_filters_by_endangered_status

- [ ] T048 [US3] Write integration test: test_species_detail_displays_regions_and_status

- [ ] T049 [US3] Run integration tests and verify all pass

---

## Phase 6: Data Migration & Cleanup

**Goal**: Safely migrate existing data from old to new schema
**Duration**: 3-4 hours
**Blocking**: Must complete before deploying to production
**Independent Test Criteria**:
- All data migrated without loss
- Record counts match before/after
- No orphaned records
- Old tables backed up
- Rollback plan verified

### Data Migration

- [ ] T050 Run migration script: `php artisan migrate --path=database/migrations/YYYY_MM_DD_migrate_endangered_data.php`

- [ ] T051 Verify data integrity:
  - Compare record counts: old vs new
  - Check foreign key constraints satisfied
  - Verify no null conservation_status values
  - Spot-check random species have correct regions/status

- [ ] T052 Archive old tables: rename `endangered_regions` → `endangered_regions_archive`, `species_endangered_region` → `species_endangered_region_archive`

- [ ] T053 Create rollback documentation: document how to restore from archive if needed

- [ ] T054 Test rollback scenario on staging database

---

## Phase 7: Testing & Quality Assurance

**Goal**: Comprehensive testing across all features
**Duration**: 2-3 hours
**Independent Test Criteria**: All tests pass, 100% coverage of user stories, no performance regressions

### Unit Tests

- [ ] T055 [P] Run all unit tests: `php artisan test tests/Unit/`

- [ ] T056 [P] Run feature tests: `php artisan test tests/Feature/`

- [ ] T057 [P] Run integration tests: `php artisan test`

### Performance Tests

- [ ] T058 [P] Verify regional map query <500ms: check `aggregateRegionData()` execution time

- [ ] T059 [P] Verify species editing page load <1s: measure SpeciesManager component load time

- [ ] T060 [P] Verify species browser filters responsive: check query performance for endangered/region filters

### Manual Testing

- [ ] T061 [US1] Manual test: add region to species via admin, verify persists on edit

- [ ] T062 [US2] Manual test: change conservation_status, verify update persists

- [ ] T063 [US3] Manual test: view regional map, verify counts correct

- [ ] T064 [US3] Manual test: view species detail, verify regions and status display

- [ ] T065 Manual test: delete region, verify cascade delete works (species_region entry removed)

---

## Phase 8: Documentation & Finalization

**Goal**: Document changes and prepare for deployment
**Duration**: 2-3 hours
**Independent Test Criteria**: Documentation complete, migration guide written, admin guide created

### Documentation

- [ ] T066 Update project README with data model changes if applicable

- [ ] T067 Create admin guide: `docs/admin-guide-regions.md` with instructions for:
  - How to add/edit regions for a species
  - How to set conservation ratings
  - Understanding the difference between distribution and endangerment

- [ ] T068 Create developer documentation: `docs/developer-regions-model.md` with:
  - Data model overview
  - Code examples for querying relationships
  - Common patterns (add region, change status, filter by region)
  - Performance tips (eager loading, indexes)

- [ ] T069 Update migration guide with data migration steps and rollback procedures

- [ ] T070 Create deprecation notices for old EndangeredRegion references in code comments

### Code Cleanup

- [ ] T071 [P] Remove or deprecate old EndangeredRegion model references in non-critical paths

- [ ] T072 [P] Add code comments explaining why regions() is preferred over endangeredRegions()

- [ ] T073 Verify no deprecated code remains in critical paths

### Final Verification

- [ ] T074 Run full test suite: `php artisan test`

- [ ] T075 Check for PHP code quality issues: `./vendor/bin/phpstan analyse app/`

- [ ] T076 Verify all git commits are clean and descriptive

- [ ] T077 Create deployment checklist document

---

## Summary & Execution Order

### Task Statistics
- **Total Tasks**: 77
- **Setup Tasks (T001-T005)**: 5
- **Foundation Tasks (T006-T013)**: 8 (required for all stories)
- **US1 Tasks (T014-T024)**: 11
- **US2 Tasks (T025-T035)**: 11
- **US3 Tasks (T036-T049)**: 14
- **Migration Tasks (T050-T054)**: 5
- **Testing Tasks (T055-T065)**: 11
- **Documentation Tasks (T066-T077)**: 12

### Parallelizable Tasks
The following tasks can be executed in parallel (different files/concerns):
- T001, T002 (different migrations)
- T006, T007 (different models)
- T010, T011, T012 (different test files)
- T021, T022, T023 (different test cases)
- T031, T032, T033, T034 (different test cases)
- T046, T047, T048 (different test cases)
- T055, T056, T057 (different test suites)
- T058, T059, T060 (different performance metrics)
- T066, T067, T068, T069 (different docs)

### Dependency Graph
```
Phase 1 (Setup)
  ↓
Phase 2 (Foundation Models)
  ↓
Phase 3 (US1: Distribution)  ⟷  Phase 4 (US2: Ratings) [parallel]
  ↓
Phase 5 (US3: Map & Browsers) [depends on 3+4]
  ↓
Phase 6 (Migration)
  ↓
Phase 7 (Testing)
  ↓
Phase 8 (Documentation)
```

### Estimated Timeline
- Phase 1: 2-4 hours
- Phase 2: 3-4 hours
- Phase 3: 6-8 hours
- Phase 4: 6-8 hours (can overlap with Phase 3)
- Phase 5: 5-6 hours
- Phase 6: 3-4 hours
- Phase 7: 2-3 hours
- Phase 8: 2-3 hours
- **Total**: 3-4 days with parallel work on Phases 3-4

### MVP Scope
Minimum viable product to deploy:
- **Phase 1**: Setup & migrations (T001-T005)
- **Phase 2**: Foundation models (T006-T009)
- **Phase 3**: US1 Admin UI (T014-T020)
- **Phase 4**: US2 Conservation Ratings (T025-T030)
- **Phase 6**: Data migration (T050-T054)

This provides core functionality for admins to manage regions and ratings. Public features (Phase 5) can follow in a subsequent release.

---

## Next Steps

1. Review task list with team
2. Assign tasks to developers
3. Track progress using checklist above
4. Update completion status as tasks finish
5. Run full test suite before deployment
6. Deploy to staging, then production

---

## References

- **Specification**: [spec.md](./spec.md)
- **Implementation Plan**: [plan.md](./plan.md)
- **Data Model**: [data-model.md](./data-model.md)
- **Quick Start**: [quickstart.md](./quickstart.md)
- **Component Contracts**: [contracts/components.md](./contracts/components.md)
