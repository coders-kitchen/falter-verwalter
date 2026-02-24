# Feature: Primary/Secondary Plant Preference per Life Stage

## Goal
Allow plant assignments per butterfly species to be classified as `primaer` or `sekundaer`, separated by life stage:
- adult butterfly (nectar)
- larva/caterpillar (host plant)

The model intentionally stays region-agnostic for now.

## User Flow
- Admin: assigns plants to a species as today (`Nektarpflanze` and/or `Futterpflanze`).
- Admin: can additionally set preference per relevant life stage (`primaer` / `sekundaer`).
- Visitor (public finder): searches only against `primaer` plant relations.
- Visitor (species detail): sees primary plants prominently and secondary plants in a de-emphasized section.

## Data Model
Extend pivot table `species_plant` with two nullable enum fields:
- `adult_preference` (`primaer` | `sekundaer` | `null`)
- `larval_preference` (`primaer` | `sekundaer` | `null`)

Existing fields remain unchanged:
- `is_nectar` (bool)
- `is_larval_host` (bool)

Notes:
- `null` means "no preference classification set" (not a third domain category).
- Database values should be ASCII-safe (`primaer`, `sekundaer`), labels in UI can be localized (`Primär`, `Sekundär`).

## Validation Rules
- `adult_preference` may only be set when `is_nectar = true`.
- `larval_preference` may only be set when `is_larval_host = true`.
- If `is_nectar = false`, force `adult_preference = null`.
- If `is_larval_host = false`, force `larval_preference = null`.
- Allowed values are strictly `primaer`, `sekundaer`, or `null`.

## Admin UI Changes
In species-plant assignment UI:
- keep usage toggles:
  - `🌺 Nektarpflanze (Adulte Falter)`
  - `🥬 Futterpflanze (Raupen)`
- add preference selects:
  - `Präferenz (Adulte)` -> `Primär` / `Sekundär` / `nicht gesetzt`
  - `Präferenz (Raupe)` -> `Primär` / `Sekundär` / `nicht gesetzt`
- disable or hide each select if corresponding usage toggle is off.

## Public Behavior Changes
### Plant-Butterfly Finder
Public matching should include only primary relations:
- adult match: `is_nectar = true` AND `adult_preference = 'primaer'`
- larval match: `is_larval_host = true` AND `larval_preference = 'primaer'`

`sekundaer` and `null` relations are excluded from normal-user search matching.

### Plant Detail
Species lists for a plant (nectar/larval) should follow the same public rule and include only primary relations.

## Species Detail Presentation
For each life stage, render two clearly separated groups:
- `Primäre Pflanzen` (prominent, default styling)
- `Sekundäre Pflanzen` (de-emphasized)

Suggested secondary styling:
- simple list
- smaller text (`text-sm`)
- muted color (`text-base-content/50` to `/60`)
- minimal/no colorful badges

## Migration Strategy
1. Add nullable enum columns to `species_plant`.
2. Backfill existing rows with `primaer` for both preferences.
3. Deploy UI + query changes.

No destructive cleanup required in this step.

## Acceptance Criteria
- Admin can set stage-specific preference (`primaer`/`sekundaer`) for each species-plant row.
- Stage-specific preference is persisted and validated correctly.
- Public finder returns matches only via primary relations.
- Species detail distinguishes primary and secondary plants per life stage.
- Secondary plants are visually present but clearly less prominent.
- Region-specific logic is not introduced.

## Out of Scope
- Regional overrides of plant preference.
- Additional ranking tiers beyond `primaer` / `sekundaer`.
- Personalized/public role-specific search variants.

## Notes
- Run migrations manually:
  - `php artisan migrate`
