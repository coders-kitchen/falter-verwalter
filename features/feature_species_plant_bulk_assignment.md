# Feature: Species Plant Bulk Assignment (Final Admin Flow)

## Goal
Enable scalable species-to-plant assignment for datasets with hundreds of plants, while keeping the existing `+ Neue Pflanzenzuordnung` workflow intact.

## Background
Original user feedback:
- Long dropdown-based selection is not usable with 100+ plants.
- Existing flow (`+` button, row-level edit/delete) should remain understandable.

## Final UX Decision (Implemented)
- Keep **current assignments first** as primary section.
- Keep **single row edit/delete** in that table.
- Keep `+ Neue Pflanzenzuordnung` as entry point.
- Add **bulk creation inside the `+` modal** (instead of replacing the page with global bulk toolbar).

## Admin User Flow
1. Open species plant assignment page.
2. See and manage **Aktuelle Zuordnungen** first.
   - search by plant name/scientific name
   - filter: `Alle`, `Nur Nektar`, `Nur Futterpflanze`, `Nektar + Futterpflanze`
   - edit or delete individual mappings
3. Click `+ Neue Pflanzenzuordnung`.
4. In modal:
   - choose usage flags (`is_nectar`, `is_larval_host`)
   - search non-assigned plants
   - multi-select plants via checkboxes
   - optional: `Alle auf Seite markieren`
5. Save once to create multiple mappings in one operation.

## Data Model
Unchanged pivot model:
- table: `species_plant`
- columns: `species_id`, `plant_id`, `is_nectar`, `is_larval_host`
- unique key: `species_id + plant_id`

## Implementation
- `app/Livewire/SpeciesPlantManager.php`
  - assigned list search/filter + pagination (`assignedPage`)
  - row-level edit/delete
  - create modal with bulk-select for not-yet-assigned plants (`addPlantsPage`)
  - duplicate-safe bulk create via `upsert`
- `resources/views/livewire/species-plant-manager.blade.php`
  - assignments-first layout
  - retained edit/delete in assignment table
  - modal-based bulk creation UI

## Bulk Create Semantics
- Create mode applies selected flags to all selected plants.
- Uses `upsert` on (`species_id`, `plant_id`) to stay duplicate-safe.
- Existing records are updated if reinserted.

## Out of Scope
- Public UI changes.
- New domain tables.
- Any change to `species_plant` schema.

## Acceptance Criteria
- Existing row-level edit/delete remains available.
- Assignment table is shown before add controls.
- `+` workflow still exists and now supports multi-select bulk add.
- Large datasets can be handled via search + paginated multi-select.
- No duplicate pivot rows are created.

## Follow-up
- Add focused feature tests for:
  - assigned list filter behavior
  - modal bulk add upsert behavior
  - row-level edit/delete behavior
