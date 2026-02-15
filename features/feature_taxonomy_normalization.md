# Feature: Taxonomy Normalization for Plants and Butterflies

## Goal
Introduce a normalized taxonomy model that supports both plants and butterflies, while keeping admin workflows simple and preserving the current structure during migration verification.

## Constraints
- Model must be shared across plants and butterflies.
- Admin selections must be type-restricted (`plant` vs `butterfly`).
- During entity creation/editing, display genus with full hierarchy path:
  - `Familie >> Unterfamilie (>> Tribus) >> Gattung`
- **Legacy structure must remain in place initially** so migration/backfill can be verified safely.

## Current Problem
The current `families` structure stores mixed hierarchy fields in one table. This causes:
- duplicate/semi-duplicate entries,
- poor structure for admin maintenance,
- difficult taxonomy filtering for users/admin.

## Target Data Model
New normalized hierarchy tables:
- `families` (existing, kept):
  - `id`, `type` (`plant|butterfly`), `name`
- `subfamilies`:
  - `id`, `family_id`, `name`
- `tribes`:
  - `id`, `subfamily_id`, `name`
- `genera`:
  - `id`, `subfamily_id`, `tribe_id` nullable, `name`

Entity references:
- `plants.genus_id` (new, nullable initially)
- `species.genus_id` (new, nullable initially)

Notes:
- `tribe_id` is optional.
- Genus is always assigned to a subfamily; tribe may be null.

## Integrity Rules
- `families(type, name)` unique
- `subfamilies(family_id, name)` unique
- `tribes(subfamily_id, name)` unique
- `genera(subfamily_id, tribe_id, name)` unique
- App-level validation: if `genera.tribe_id` is set, tribe must belong to the same subfamily.

## Admin UX Requirements
- Create/edit plant: genus dropdown/search restricted to `family.type = plant`.
- Create/edit butterfly species: genus dropdown/search restricted to `family.type = butterfly`.
- Each genus option should show path label:
  - with tribe: `Fam >> Subfam >> Tribe >> Genus`
  - without tribe: `Fam >> Subfam >> Genus`

## User/Admin Filtering Requirements
Support filtering by any taxonomy level:
- family
- subfamily
- tribe (optional)
- genus

## Migration Strategy (Safe, Verifiable)
Phase 1: Schema introduction
1. Create `subfamilies`, `tribes`, `genera`.
2. Add nullable `genus_id` to `plants` and `species`.
3. Keep legacy fields and existing relations unchanged.

Phase 2: Backfill
1. Parse legacy `families` rows (`name`, `subfamily`, `tribe`, `genus`, `type`).
2. Upsert normalized hierarchy records.
3. Set `plants.genus_id` and `species.genus_id`.
4. Produce verification counts/report (mapped/unmapped).

Phase 3: Dual-read period
1. UI and queries can read new hierarchy while legacy remains available.
2. Verify admin and public filters against real data.

Phase 4: Cleanup (later, separate task)
1. Remove legacy redundant taxonomy fields/relations only after verification approval.

## Out of Scope (this feature step)
- Deleting old taxonomy columns.
- Hard switch that removes legacy fallback immediately.
- Large UI redesign outside taxonomy selection/filtering.

## Acceptance Criteria
- New hierarchy tables exist and are populated from legacy data.
- `plants` and `species` can reference `genus_id`.
- Admin can select genus with correct type restriction and full hierarchy label.
- Filtering by subfamily and other levels is possible.
- Legacy structure still exists after rollout for validation.

## Follow-up Tasks
- Implement migrations and backfill command/seeder.
- Add automated verification checks for mapping completeness.
- Switch read paths fully to normalized model after stakeholder sign-off.
