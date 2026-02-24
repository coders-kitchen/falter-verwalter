---
name: feature-release-closure
description: Run this skill when a feature is complete (e.g. user says "feature fertig"). It ensures release closure by adding/updating changelog entries, validating seeding/migrations impact, and preparing the final commit checklist.
---

# Feature Release Closure

Use this skill whenever a user indicates a feature is done, especially with phrases like:
- `feature fertig`
- `feature abgeschlossen`
- `ready to ship`

## Goal
Close a feature with consistent release hygiene:
1. Changelog entry exists and is audience-correct (`public`, `admin`, or `both`).
2. Admin/public impact is clearly separated in `details` using:
   - `Public: ...`
   - `Admin: ...`
3. Seeder data is updated in `database/seeders/ChangelogEntrySeeder.php`.
4. Migration and seeding instructions are provided if schema/data changed.
5. Final pre-commit checks are run.

## Inputs To Collect
- Feature source doc path (default: latest `features/feature_*.md` changed in git).
- Intended audience (`public`, `admin`, `both`).
- Version label (recommend format `YYYY.MM.DD.N`).
- Optional GitHub commit links.

## Workflow
1. Identify the finished feature scope from changed files and matching `features/feature_*.md`.
2. Summarize user-facing impact and admin-facing impact in one sentence each.
3. Create or update one changelog row in `ChangelogEntrySeeder`:
   - `title`: concise, user-readable
   - `summary`: concise, high-value message
   - `details`: include segmented `Public:` / `Admin:` lines
   - `audience`: set to `public`, `admin`, or `both`
   - `published_at`: use current timestamp unless user requests a specific one
   - `commit_refs`: include provided commit URL(s)
4. Ensure `DatabaseSeeder` calls `ChangelogEntrySeeder`.
5. Run checks:
   - `php -l` on changed PHP files
   - `php artisan test` (if available)
6. Report:
   - what changelog entry was added/updated
   - any follow-up command needed (`php artisan migrate`, `php artisan db:seed`)

## Commit Strategy (Default + Optional)
- Default: create a separate commit for changelog closure.
- Optional: use `--amend` only when all conditions are true:
  - the feature commit is the local `HEAD`
  - the feature commit is not pushed/shared yet
  - the user explicitly asks to fold changelog into the feature commit
- If any condition is false or unknown, prefer a separate commit.
- Always state which strategy was used.

## Quality Rules
- Prefer concrete user value over implementation details.
- Keep `summary` short (1 sentence).
- Avoid duplicate versions; use `updateOrCreate` semantics.
- If impact is admin-only, do not pollute public changelog with technical noise.

## Exit Criteria
- Seeder entry added/updated.
- Audience and segmented details are correct.
- Checks executed and reported.
