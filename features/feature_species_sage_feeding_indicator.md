# Feature: Species Sage Feeding Indicator

## Goal
Track whether a butterfly species also feeds on sage species.

## User Flow
- Admin (create/edit species): selects one of three values.
- Visitor (species detail): sees the selected value.

## Allowed Values
- `Ja`
- `Nein`
- `keine genaue Angabe`

## Data Model
New field on `species`:
- `sage_feeding_indicator` (string)

Default:
- `keine genaue Angabe`

Migration:
- `database/migrations/2026_02_15_233000_add_sage_feeding_indicator_to_species_table.php`

## Backend Changes
- `app/Models/Species.php`
  - adds `sage_feeding_indicator` to `$fillable`
- `app/Livewire/SpeciesManager.php`
  - adds form field, default, validation, and persistence handling
- `app/Http/Requests/SpeciesRequest.php`
  - validates field with strict enum values
- `app/Http/Resources/SpeciesResource.php`
  - includes `sage_feeding_indicator` in API response

## UI Changes
- `resources/views/livewire/species-manager.blade.php`
  - adds admin select input for the three states
- `resources/views/livewire/public/species-detail.blade.php`
  - displays value in species taxonomy/details section

## Acceptance Criteria
- Admin can set exactly one of the three states.
- Value is persisted and shown when editing the species.
- Public species detail shows the value.
- API validates and returns the value consistently.

## Notes
- Apply migration with:
  - `php artisan migrate`
