# Implementation Plan: Endangered Regions Model Refactoring

**Branch**: `002-endangered-regions-model` | **Date**: 2025-11-02 | **Spec**: [spec.md](./spec.md)
**Input**: Feature specification from `/specs/002-endangered-regions-model/spec.md`

## Summary

Refactor the endangered species data model to separate geographic distribution from conservation status. Currently conflated, the new model enables tracking which regions a species naturally occurs in, independently from its conservation rating in each region. This requires database migrations, new Eloquent relationships, UI updates for species management, and public feature updates to properly display species occurrence vs. endangerment status.

## Technical Context

**Language/Version**: PHP 8.2 (Laravel 12)
**Primary Dependencies**: Laravel Eloquent ORM, Livewire 3.6.4, MySQL 8.0
**Storage**: MySQL 8.0 (relational database)
**Testing**: Pest/PHPUnit for unit and integration tests
**Target Platform**: Web application (Linux server)
**Project Type**: Laravel web application (MVC with Livewire components)
**Performance Goals**: Species editing page load <1s, region queries <500ms, pagination responsive
**Constraints**: Must maintain backward compatibility with existing species records during migration
**Scale/Scope**: ~100-1000 species records, ~10-20 regions, multi-user concurrent editing

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

| Principle | Requirement | Status | Evidence |
|-----------|-------------|--------|----------|
| **I. Benutzerfreundlichkeit** | UI must clearly separate region selection from rating assignment | ✅ PASS | Spec FR4 requires visually distinct sections in admin form |
| **II. Datenintegrität** | All region-species pairings must have ratings (no nulls); cascade deletes required | ✅ PASS | Spec FR3 enforces ratings requirement; migration strategy ensures no orphaned data |
| **III. Wartbarkeit** | Code follows Laravel best practices; relationships properly modeled | ✅ PASS | Using Eloquent BelongsToMany relationship pattern; standard pivot table design |
| **IV. Suchbarkeit** | Region filters must work correctly after model change | ✅ PASS | Spec requires updated public features; SpeciesBrowser already supports region filtering |
| **V. Dokumentation** | Architecture changes must be documented; admin instructions provided | ✅ PASS | Spec includes data model diagram and user scenarios; quickstart.md will document |
| **Sicherheit** | Only admins can assign conservation ratings | ✅ PASS | Admin-only CRUD operations through authenticated Livewire components |
| **Performance** | Region queries <500ms; species editing <1s | ✅ PASS | Indexed relationships; eager loading reduces N+1 queries |

**Gate Status**: ✅ **PASS** - All constitutional requirements satisfied

## Project Structure

### Documentation (this feature)

```text
specs/[###-feature]/
├── plan.md              # This file (/speckit.plan command output)
├── research.md          # Phase 0 output (/speckit.plan command)
├── data-model.md        # Phase 1 output (/speckit.plan command)
├── quickstart.md        # Phase 1 output (/speckit.plan command)
├── contracts/           # Phase 1 output (/speckit.plan command)
└── tasks.md             # Phase 2 output (/speckit.tasks command - NOT created by /speckit.plan)
```

### Source Code (repository root)

```text
app/
├── Models/
│   ├── Species.php               # Updated: add regions() relationship
│   ├── Region.php                # New: geographic region model
│   ├── SpeciesRegion.php         # New: pivot model with conservation_status
│   └── EndangeredRegion.php       # Deprecated: to be archived after migration
├── Livewire/
│   ├── SpeciesManager.php        # Updated: add region assignment UI
│   └── Public/
│       ├── SpeciesBrowser.php   # Updated: new filter queries
│       └── RegionalDistributionMap.php # Updated: new count queries
└── Http/Controllers/
    └── (API endpoints as needed)

database/
├── migrations/
│   ├── YYYY_MM_DD_create_regions_table.php         # New
│   ├── YYYY_MM_DD_create_species_region_table.php  # New with enum
│   └── YYYY_MM_DD_migrate_endangered_data.php      # New (data migration)
└── factories/
    └── SpeciesRegionFactory.php  # New: for testing

resources/views/
├── livewire/
│   └── species-manager.blade.php # Updated: region assignment UI
└── public/
    └── species-detail.blade.php  # Updated: conservation status display

tests/
├── Feature/
│   └── SpeciesRegionManagementTest.php # New
├── Unit/
│   └── Models/SpeciesRegionTest.php   # New
└── data/
    └── species-region-fixtures.php    # New: test data

```

**Structure Decision**: Feature is contained within existing Laravel MVC structure. Only new files are database models, migration, and relationship methods. Updates are minimal, focused, and backward-compatible.

## Implementation Approach

### Phase 0: Research & Planning (Completed)
- ✅ Constitution Check passed
- ✅ Technical context established (Laravel 12 + Eloquent)
- ✅ Architecture verified as sound and maintainable

### Phase 1: Design & Contracts (Proceeding)
This plan document + generated artifacts:
1. **data-model.md** - Entity definitions and relationships
2. **contracts/** - API/component contracts
3. **quickstart.md** - Getting started guide for implementation

### Phase 2: Task Generation (/speckit.tasks)
- Generates detailed, actionable tasks from this plan
- Tasks ordered with dependencies
- Includes testing requirements

### Implementation Strategy
1. **Database First**: Create migrations and models
2. **Relationships**: Implement Eloquent relationships
3. **Admin UI**: Update species manager with region assignment
4. **Data Migration**: Migrate existing endangered_region data
5. **Public Features**: Update species browser and map
6. **Testing**: Unit and integration tests
7. **Documentation**: Admin guides and technical docs
