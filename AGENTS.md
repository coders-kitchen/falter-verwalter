## Skills
A skill is a set of local instructions to follow that is stored in a `SKILL.md` file.

### Available skills
- feature-release-closure: Close a finished feature with release hygiene (changelog entry, audience split, checks, and final closure steps). Use when the user says `feature fertig`, `feature abgeschlossen`, or equivalent. (file: /home/peter/Development/falter-verwalter-v2-fresh/skills/feature-release-closure/SKILL.md)
- skill-creator: Guide for creating effective skills. (file: /home/peter/.codex/skills/.system/skill-creator/SKILL.md)
- skill-installer: Install Codex skills into $CODEX_HOME/skills from curated or repo sources. (file: /home/peter/.codex/skills/.system/skill-installer/SKILL.md)

### Trigger rules
- If the user explicitly says `feature fertig` (or equivalent), the `feature-release-closure` skill MUST be used.
- If the user asks to create or update a skill, `skill-creator` MUST be used.
- If the user asks to list/install skills, `skill-installer` MUST be used.

### How to use skills
1. Open the referenced `SKILL.md`.
2. Follow only the instructions needed for the current request.
3. Keep context lean and avoid loading unrelated files.
4. Report what was changed and what remains.

### Safety
- If a skill file cannot be read, say so briefly and continue with best-effort fallback.
- Do not assume a skill is active across turns unless retriggered.
