# Feature: Auto Generation Numbering and Derived Generations Per Year

## Goal
- Generation numbers are no longer entered manually by admins.
- `species.generations_per_year` is automatically derived from existing generations.
- Generation numbering remains gapless after deletions.

## User Flow
- Admin creates a generation: only lifecycle fields are entered (months, description).
- System assigns generation number automatically (`1, 2, 3, ...`).
- Admin deletes a generation: remaining generations are renumbered to close gaps.
- `Generationen pro Jahr` is no longer manually edited in the species form.
- Public view behavior remains unchanged.

## Data Behavior
- Generation number assignment is model-driven in `Generation` lifecycle hooks.
- Species `generations_per_year` is synced to the current generation count.

## Implementation
- `app/Models/Generation.php`
  - auto-assign next `generation_number` on create
  - renumber generations per species after delete
  - sync `species.generations_per_year` after create/delete
- `app/Livewire/GenerationManager.php`
  - removes generation number input/validation from admin flow
- `resources/views/livewire/generation-manager.blade.php`
  - removes generation number field from modal
- `app/Livewire/SpeciesManager.php`
  - removes manual `generations_per_year` editing from form state/validation
- `resources/views/livewire/species-manager.blade.php`
  - removes `Generationen pro Jahr` input field in species modal
- `app/Http/Requests/SpeciesRequest.php`
  - removes `generations_per_year` request validation

## Backfill Migration
- `database/migrations/2026_02_16_000100_sync_species_generations_per_year_counts.php`
  - one-time synchronization of existing species data
  - sets `generations_per_year = count(generations)` per species

## Acceptance Criteria
- Admin cannot set generation number manually.
- New generations get incremental numbers automatically.
- After deleting a middle generation, numbering is gapless.
- Species `generations_per_year` equals the number of generations.
- Public pages continue showing generation data as before.

## Notes
- Apply migrations manually as needed:
  - `php artisan migrate`
