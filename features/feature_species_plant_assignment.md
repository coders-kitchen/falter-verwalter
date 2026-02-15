# Feature: Species-Based Plant Assignment (Nectar/Larval)

## Goal
Move plant usage assignment from generation level to species level.

## User Flow
- Admin: manages nectar plants and larval host plants in a dedicated section per species.
- Admin: does not assign plants inside generation create/edit anymore.
- Visitor: can still filter species by selected plants and see if usage is nectar and/or larval.
- Visitor: can still open plant detail and see which species use the plant as nectar and/or larval host.

## Data Model
Pivot table `species_plant` is now the source of truth.

Columns:
- `species_id`
- `plant_id`
- `is_nectar` (bool)
- `is_larval_host` (bool)

Unique key remains:
- `unique(species_id, plant_id)`

Migration:
- `database/migrations/2026_02_15_234000_refactor_species_plant_to_flags_and_migrate_generation_data.php`

## Migration Behavior
- Adds `is_nectar` and `is_larval_host` to `species_plant`.
- Migrates previous `plant_type` values into these flags.
- Aggregates plant usage from `generations.nectar_plants` and `generations.larval_host_plants`.
- Upserts by `species_id + plant_id`, preventing duplicates.
- Merges existing and migrated data with OR semantics.
- Drops legacy `plant_type` column after migration.

## Admin UI Changes
- New manager:
  - `app/Livewire/SpeciesPlantManager.php`
  - `resources/views/livewire/species-plant-manager.blade.php`
- New admin page:
  - `resources/views/admin/species-plants.blade.php`
- New route:
  - `admin.speciesPlants.index` (`/admin/species/{species}/speciesPlants`)
- Species list action button now includes "Pflanzen":
  - `resources/views/livewire/species-manager.blade.php`
- Generation manager no longer edits plant assignments:
  - `app/Livewire/GenerationManager.php`
  - `resources/views/livewire/generation-manager.blade.php`

## Public Behavior Changes
- Plant finder now filters via `species_plant` flags, not generation JSON:
  - `app/Livewire/Public/PlantButterflyFinder.php`
  - `resources/views/livewire/public/plant-butterfly-finder.blade.php`
- Plant detail queries species via pivot flags:
  - `app/Livewire/Public/PlantDetail.php`
- Species detail lists nectar/larval plants directly per species:
  - `app/Livewire/Public/SpeciesDetail.php`
  - `resources/views/livewire/public/species-detail.blade.php`

## Model/API Updates
- `app/Models/Species.php`
  - adds `plants()`, `nectarPlants()`, `larvalHostPlants()`
  - keeps `hostPlants()` as alias to `larvalHostPlants()`
- `app/Models/Plant.php`
  - updates pivot fields and adds filtered relations
- `app/Models/SpeciesPlant.php`
  - new model for dedicated admin management
- `app/Http/Controllers/SpeciesController.php`
  - uses new pivot fields for API host plant assignment
- `app/Http/Resources/SpeciesResource.php`
  - exposes plant mappings with usage flags when loaded

## Acceptance Criteria
- Admin can assign plants to a species with nectar and/or larval usage.
- Admin cannot create duplicate species/plant rows.
- Generation forms no longer contain plant assignment.
- Visitor plant finder, species detail, and plant detail still show correct plant usage.
- Migration copies existing generation-based data without creating duplicates.

## Notes
- Run migrations manually:
  - `php artisan migrate`
