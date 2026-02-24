# Feature: Species Assignment to Plant Species or Plant Genus

## Goal
Allow admins to assign either
- concrete plant species, or
- entire plant genera

to a butterfly species for nectar and/or larval feeding relationships.

The workflow must stay simple and consistent with the current bulk assignment UX.

## Background
In some cases, feeding is known only at genus level (many species, uncertain exact species, or ambiguous records).
The system must represent this explicitly without forcing incorrect plant-species precision.

## User Flow
- Admin opens species assignment dialog (`Pflanzenzuordnung`).
- Admin chooses assignment mode:
  - `Art` (plant species)
  - `Gattung` (plant genus)
- In both modes, admin uses the same checkbox-table bulk selection pattern:
  - search
  - paginated table with checkboxes
  - `Alle auf Seite markieren`
  - bulk save
- Admin sets usage flags and stage preferences:
  - `Nektarpflanze` / `Futterpflanze`
  - `Primär` / `Sekundär` per life stage (when relevant)

## Data Model
Use two dedicated pivot tables.

### Existing table (keep)
`species_plant`
- `species_id`
- `plant_id`
- `is_nectar`
- `is_larval_host`
- `adult_preference` (`primaer|sekundaer|null`)
- `larval_preference` (`primaer|sekundaer|null`)

Unique:
- `unique(species_id, plant_id)`

### New table
`species_genus`
- `species_id`
- `genus_id`
- `is_nectar`
- `is_larval_host`
- `adult_preference` (`primaer|sekundaer|null`)
- `larval_preference` (`primaer|sekundaer|null`)
- timestamps

Unique:
- `unique(species_id, genus_id)`

Constraints:
- `genus_id` must reference plant genera only (family type = `plant`) via app-level validation/filtering.

## Validation Rules
For both assignment types (plant/genus):
- At least one usage must be selected (`is_nectar` or `is_larval_host`).
- If `is_nectar = true`, `adult_preference` is required in UI (`primaer|sekundaer`).
- If `is_larval_host = true`, `larval_preference` is required in UI (`primaer|sekundaer`).
- If usage is false, corresponding preference is stored as `null`.

## Admin UI Changes
Primary objective: one simple, consistent modal UX.

Modal changes:
- Add assignment type switch: `Art` / `Gattung`.
- Keep one shared bulk-selection layout for both modes.
- In `Art` mode:
  - list unassigned plant species.
- In `Gattung` mode:
  - list unassigned genera.
- For both modes:
  - checkbox table, pagination, "select all on page", clear selection.
  - same usage + preference controls.

Overview table changes:
- Show mixed assignments in one list.
- Add column `Typ` with values:
  - `Art`
  - `Gattung`
- Name rendering:
  - species assignment: plant name
  - genus assignment: `Genusname (sp.)`

## Public Behavior Changes
### Plant-Butterfly Finder
User-selected plant should match a species when at least one of these is true (primary only):
- direct species-level plant assignment is primary for relevant stage, or
- genus-level assignment exists for `plant.genus_id` and is primary for relevant stage.

Stage logic remains separated:
- adult: nectar + `adult_preference = primaer`
- larval: host + `larval_preference = primaer`

### Plant Detail
Species listed for a plant should include:
- direct primary assignments to that plant
- primary genus assignments matching the plant genus

Optional label in list:
- direct: no suffix
- genus-based: `via Gattung (sp.)`

### Species Detail
Plant association sections should include both sources:
- concrete species plants
- genus entries shown as `Genusname (sp.)`

Keep current visual separation:
- primäre Pflanzen (prominent)
- sekundäre Pflanzen (de-emphasized)

## Query/Performance Notes
- Add index on `species_genus.genus_id` and `species_genus.species_id`.
- For public matching, prefer `whereExists`/`whereHas` strategy to avoid duplicate rows.
- Use `distinct()` where needed when combining direct + genus matches.

## Migration Strategy
1. Create `species_genus` table.
2. No destructive changes to existing `species_plant`.
3. Deploy admin modal changes with mode switch + genus bulk selection.
4. Deploy public query updates.

No data backfill required for genus assignments in this step.

## Acceptance Criteria
- Admin can assign multiple plant species in one action (existing behavior remains).
- Admin can assign multiple plant genera in one action using the same checkbox-table pattern.
- Admin can set stage-specific primary/secondary preferences for genus assignments.
- Mixed assignments are visible in one overview, clearly marked as `Art` or `Gattung`.
- Genus rows are identifiable as `(sp.)`.
- Public finder and plant detail correctly match genus assignments for selected plants.
- Public matching for normal users stays restricted to `primaer` relationships.

## Out of Scope
- Regional overrides of genus/species feeding.
- Automatic conversion of plant assignments into genus assignments.
- Additional certainty levels beyond primary/secondary.

## Notes
- Run migrations manually:
  - `php artisan migrate`
