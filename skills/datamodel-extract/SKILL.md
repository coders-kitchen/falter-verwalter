---
name: datamodel-extract
description: Extract the current Laravel data model into reusable context files from app/Models and database/migrations. Use when users ask for current data-model context, schema documentation, or after schema-relevant file changes.
---

# Data Model Extract

Use this skill when the current Laravel data model should be externalized into compact context files.

## When to run

- After changes in `app/Models/*.php`
- After changes in `database/migrations/*.php`
- When a task needs current schema or relation context without reparsing the whole codebase

## Workflow

1. Assume schema/model changes are already in a stable state.
2. Run `php skills/datamodel-extract/scripts/extract_datamodel.php`.
3. Review the generated files under `docs/context/data-model/`.
4. If the generated output conflicts with intent, inspect the relevant migrations or model relations and rerun after fixes.

## Output

The extractor writes:

- `docs/context/data-model/schema.md`
- `docs/context/data-model/relations.md`
- `docs/context/data-model/schema.json`

## Notes

- The extractor is static and derives structure from migration source plus Eloquent model definitions.
- Treat generated files as a working context snapshot, not as a substitute for migrations.
- Prefer reading the generated files first during follow-up tasks; only reopen migrations/models when something looks inconsistent.
