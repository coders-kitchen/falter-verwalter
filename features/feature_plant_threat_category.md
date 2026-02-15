# Feature: Threat Categories on Plants

## Goal
Allow plants to have a direct threat category assignment.

## User Flow
- Admin (create/edit plant): selects a threat category for a plant.
- Visitor (plant detail): sees the plant’s threat category displayed in a clear visual form.

## Out of Scope
- No distribution-area based threat modeling for plants.
- No species-style pivot logic for plant threat assignment.

## Data Model
New field on `plants`:
- `threat_category_id` (nullable foreign key to `threat_categories.id`)

Migration:
- `database/migrations/2026_02_15_232000_add_threat_category_id_to_plants_table.php`

## Backend Changes
- `app/Models/Plant.php`
  - adds `threat_category_id` to `$fillable`
  - adds `threatCategory()` relation
- `app/Http/Requests/PlantRequest.php`
  - validates `threat_category_id` as `nullable|exists:threat_categories,id`
- `app/Http/Resources/PlantResource.php`
  - includes `threat_category` object (id/code/label/color_code) when loaded
- `app/Http/Controllers/PlantController.php`
  - eager loads `threatCategory` in index/show/store/update responses

## Admin UI Changes
- `app/Livewire/PlantManager.php`
  - loads available threat categories
  - supports selecting and saving `threat_category_id`
  - supports loading selected value on edit
- `resources/views/livewire/plant-manager.blade.php`
  - replaces free text threat status input with category dropdown
  - includes “Keine Kategorie” option

## Public UI Changes
- `app/Livewire/Public/PlantDetail.php`
  - eager loads `threatCategory`
- `resources/views/livewire/public/plant-detail.blade.php`
  - displays threat category as badge using category color
  - shows “Keine Kategorie” when not set

## Acceptance Criteria
- Admin can assign a threat category to a plant.
- Admin can clear assignment (set no category).
- Selected category persists and reappears on edit.
- Plant detail page shows assigned category for visitors.
- API returns assigned category data when present.

## Notes
- Run migrations to apply schema change:
  - `php artisan migrate`
