# Feature: Schwermetallresistenz Indicator

## Goal
Add a dedicated plant indicator for heavy metal resistance.

## User Flow
- Admin (create/edit plant): selects one heavy-metal-resistance value.
- Visitor (plant detail): can view the selected value.

## Data Model
New column on `plants`:
- `heavy_metal_resistance` (string)

Allowed values:
- `nicht schwermetallresistent`
- `mäßig schwermetallresistent`
- `ausgesprochen schwermetallresistent`

Default:
- `nicht schwermetallresistent`

Migration:
- `database/migrations/2026_02_15_231000_add_heavy_metal_resistance_to_plants_table.php`

## Backend Changes
- `app/Models/Plant.php`
  - adds `heavy_metal_resistance` to `$fillable`
  - adds `HEAVY_METAL_RESISTANCE_LEVELS` constant
- `app/Http/Requests/PlantRequest.php`
  - validates field against allowed values
- `app/Http/Resources/PlantResource.php`
  - includes `heavy_metal_resistance` in API output
- `app/Livewire/PlantManager.php`
  - form default, load, and validation rules updated

## UI Changes
- `resources/views/livewire/plant-manager.blade.php`
  - admin select field for `Schwermetallresistenz`
- `resources/views/livewire/public/plant-detail.blade.php`
  - displays `Schwermetallresistenz`

## Acceptance Criteria
- Admin can select exactly one allowed heavy-metal-resistance value.
- Value is stored and shown in edit forms.
- Public plant detail shows the selected value.
- Validation rejects out-of-list values.

## Notes
- Apply migration with `php artisan migrate`.
