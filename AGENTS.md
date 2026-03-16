## Context

### Data Model

Use the generated files in `docs/context/data-model` as the default entry point for understanding the current data model.
Only open migrations or Eloquent models directly when the generated context seems stale, incomplete, or inconsistent.

| file | description |
| ---- | ----------- |
| relations.md | describes relations between data-model elements |
| schema.md    | describes the schema in markdown |
| schema.json  | describes the schema in JSON |

## Repo Skills
This section defines the repository-specific skill rules for this project.
A skill is a set of local instructions to follow that is stored in a `SKILL.md` file.

### Available skills
- feature-release-closure: Close a finished feature with release hygiene (changelog entry, audience split, checks, and final closure steps). Use when the user says `feature fertig`, `feature abgeschlossen`, or equivalent. (file: /home/peter/Development/falter-verwalter-v2-fresh/skills/feature-release-closure/SKILL.md)
- datamodel-extract: Extract the current Laravel data model into reusable context files based on migrations and Eloquent models. Use after schema-relevant changes or when current data-model context is needed. (file: /home/peter/Development/falter-verwalter-v2-fresh/skills/datamodel-extract/SKILL.md)
- skill-creator: Guide for creating effective skills. (file: /home/peter/.codex/skills/.system/skill-creator/SKILL.md)
- skill-installer: Install Codex skills into $CODEX_HOME/skills from curated or repo sources. (file: /home/peter/.codex/skills/.system/skill-installer/SKILL.md)

### Trigger rules
- If the user explicitly says `feature fertig` (or equivalent), the `feature-release-closure` skill MUST be used.
- If the user asks to create or update a skill, `skill-creator` MUST be used.
- If the user asks to list/install skills, `skill-installer` MUST be used.
- If the user asks to extract, refresh, document, or externalize the current data model, the `datamodel-extract` skill MUST be used.
- If a task changes schema-relevant files such as `app/Models/*.php`, `database/migrations/*.php`, or pivot models under `app/Models`, the `datamodel-extract` skill SHOULD be run before follow-up logic or layout work continues.

### Workflow rules
1. Prefer finishing schema and model changes first.
2. Commit or otherwise stabilize the schema-related changes before running the extractor.
3. Run the `datamodel-extract` skill to refresh the generated context files under `docs/context/data-model/`.
4. Use the generated context files as the default data-model reference for follow-up UI, logic, and API work.
5. If later changes alter the schema again, rerun the extractor before treating the context files as current.

### How to use skills
1. Open the referenced `SKILL.md`.
2. Follow only the instructions needed for the current request.
3. Keep context lean and avoid loading unrelated files.
4. Report what was changed and what remains.

### Safety
- If a skill file cannot be read, say so briefly and continue with best-effort fallback.
- Generated context files are derived artifacts and can become stale; refresh them after schema-relevant changes.
- Do not assume a skill is active across turns unless retriggered.
