# Feature: Ecological Indicator State (`number` / `X` / `?`)

## Goal
Support three mutually exclusive states for each ecological indicator value on plants:
- numeric value
- `X` = indifferentes Verhalten
- `?` = ungeklärtes Verhalten

## User Flow
- Admin (create/edit plant): for each indicator, choose exactly one state (`Zahl`, `X`, `?`).
- Visitor (plant detail): sees each indicator as either number, `X`, or `?`.

## Scope
Indicators covered:
- `light_number`
- `salt_number`
- `temperature_number`
- `continentality_number`
- `reaction_number`
- `moisture_number`
- `moisture_variation`
- `nitrogen_number`

## Data Model
Added state columns on `plants`:
- `light_number_state`
- `salt_number_state`
- `temperature_number_state`
- `continentality_number_state`
- `reaction_number_state`
- `moisture_number_state`
- `moisture_variation_state`
- `nitrogen_number_state`

Allowed state values:
- `numeric`
- `x`
- `unknown`

Migration:
- `database/migrations/2026_02_15_230000_add_indicator_states_to_plants_table.php`

## Backend Changes
- `app/Models/Plant.php`
  - includes new fillable state fields
  - adds `indicatorDisplay(string $field): string`
- `app/Livewire/PlantManager.php`
  - state fields added to form
  - validation enforces valid state and conditional numeric requirement
  - normalization sets numeric value to `null` if state is `x`/`unknown`
- `app/Http/Requests/PlantRequest.php`
  - API/controller validation updated with same rules
  - post-validation ensures no numeric value is sent for non-numeric states
- `app/Http/Resources/PlantResource.php`
  - ecological scales now expose `{ value, state }`

## UI Changes
- `resources/views/livewire/plant-manager.blade.php`
  - per indicator: state selector (`Zahl`, `X`, `?`)
  - range input shown only in `numeric` state
- `resources/views/livewire/public/plant-detail.blade.php`
  - renders indicator values via `indicatorDisplay()`

## Acceptance Criteria
- Admin can set each indicator to number, `X`, or `?`.
- States are mutually exclusive by design.
- Numeric value is required only when state is `numeric`.
- Numeric value is not persisted for `X`/`?` states.
- Plant detail page displays number / `X` / `?` correctly.

## Notes
- Apply migration with `php artisan migrate`.
