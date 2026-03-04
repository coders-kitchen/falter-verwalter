# Feature: Phagie-Stufen je Lebensstadium bei Pflanzen- und Gattungszuordnungen

## Goal
Allow admins to classify feeding breadth per life stage when assigning
- concrete plant species, or
- plant genera

to a butterfly species.

The classification should be captured separately for
- adult butterflies
- larvae/caterpillars

## User Flow
- Admin opens the species assignment dialog (`Pflanzenzuordnung`).
- Admin selects whether the relation applies to
  - `Nektarpflanze` (adult), and/or
  - `Futterpflanze` (larva).
- For each active life stage, admin can set:
  - preference: `Primär` / `Sekundär`
  - phagy level: `Unbekannt` / `Monophag` / `Oligophag` / `Polyphag`
- The same workflow must work for both:
  - plant-species assignments
  - genus assignments

## Data Model
Extend both assignment tables with nullable enum fields.

### `species_plant`
- `adult_phagy_level` (`unbekannt|monophag|oligophag|polyphag|null`)
- `larval_phagy_level` (`unbekannt|monophag|oligophag|polyphag|null`)

### `species_genus`
- `adult_phagy_level` (`unbekannt|monophag|oligophag|polyphag|null`)
- `larval_phagy_level` (`unbekannt|monophag|oligophag|polyphag|null`)

Notes:
- `null` is only valid when the corresponding usage flag is off.
- If an existing assignment already uses a life stage and no phagy data exists yet, migrate it to `unbekannt`.
- Database values remain ASCII-safe; UI labels can be localized.

## Validation Rules
- `adult_phagy_level` may only be set when `is_nectar = true`.
- `larval_phagy_level` may only be set when `is_larval_host = true`.
- If `is_nectar = false`, force `adult_phagy_level = null`.
- If `is_larval_host = false`, force `larval_phagy_level = null`.
- Allowed values are:
  - `unbekannt`
  - `monophag`
  - `oligophag`
  - `polyphag`

## Admin UI Changes
In the existing species plant/genus assignment modal:
- keep the current usage toggles
- keep the current primary/secondary preference selects
- add one phagy select per active life stage:
  - `Phagie-Stufe (Adulte)`
  - `Phagie-Stufe (Raupe)`

Select options:
- `Unbekannt`
- `Monophag`
- `Oligophag`
- `Polyphag`

Overview table:
- show phagy level together with the existing life-stage preference
- keep mixed plant/genus rows in one list

## Public Behavior
No matching logic changes are required in this step.

Public finder and public plant detail should continue to use only the existing
primary/secondary stage preference logic for filtering.

Phagy levels are additional classification metadata in this feature.

## API / Export Behavior
Where species-plant pivot data is already exposed, include the new phagy fields:
- `adult_phagy_level`
- `larval_phagy_level`

## Migration Strategy
1. Add nullable phagy enum columns to `species_plant`.
2. Add nullable phagy enum columns to `species_genus`.
3. Backfill active existing assignments to `unbekannt`.
4. Deploy admin UI and persistence changes.

## Acceptance Criteria
- Admin can store phagy levels for adult and larval relations on plant assignments.
- Admin can store phagy levels for adult and larval relations on genus assignments.
- Existing active assignments are backfilled to `unbekannt`.
- Overview rows show the stored phagy labels.
- Public matching behavior remains unchanged.

## Out of Scope
- Using phagy levels for public filtering or ranking.
- More granular certainty scales.
- Automated derivation of phagy levels from taxonomy.

## Notes
- Run migrations manually:
  - `php artisan migrate`
