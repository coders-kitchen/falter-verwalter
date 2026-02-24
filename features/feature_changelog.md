# Feature: Changelog for Admin and Public Users

## Goal
Provide transparent release communication for both audiences:
- public visitors (simple "Was ist neu" page)
- admins (persistent changelog section + one-time update modal)

## User Flow
### Public
- Visitor sees menu entry `Was ist neu`.
- Visitor opens changelog page with published updates in reverse chronological order.

### Admin
- Admin sees menu entry `Changelog` in admin navigation.
- Admin can always open the changelog page from the menu.
- After login, if there are unseen updates, admin gets a one-time modal with latest entries.
- On confirm (`Verstanden`), current latest version is marked as seen for that admin.

## Data Model
### New table: `changelog_entries`
Fields:
- `id`
- `version` (string, unique; e.g. `2026.02.24.1`)
- `title` (string)
- `summary` (text, short markdown/plain text)
- `details` (long text, optional)
- `audience` (enum: `public`, `admin`, `both`)
- `published_at` (timestamp, indexed)
- `is_active` (bool, default true)
- `commit_refs` (json, nullable)
  - array of objects:
    - `sha` (string)
    - `url` (string)
    - `label` (string, optional)
- timestamps

### Extend `users`
- `last_seen_changelog_version` (string, nullable)

## Validation Rules
For changelog CRUD (admin):
- `version` required, unique, max length (e.g. 50)
- `title` required
- `summary` required
- `audience` in (`public`, `admin`, `both`)
- `published_at` required date
- `commit_refs` optional array
- each commit URL must match GitHub commit URL pattern:
  - `https://github.com/{owner}/{repo}/commit/{sha}`

## Admin UI Changes
### Admin Menu
- Add navigation item: `Changelog`

### Admin Changelog Manager
- List entries (version, title, audience, published_at, active)
- Create/edit entries
- Toggle active state
- Manage commit links (0..n links)

### Admin Login Modal (one-time)
- Display only if newest visible/admin-relevant version is newer than `last_seen_changelog_version`
- Shows latest N entries (e.g. last 3)
- CTA: `Verstanden`
- On CTA:
  - update `users.last_seen_changelog_version` to latest shown version

## Public UI Changes
### Public Menu
- Add navigation item: `Was ist neu`

### Public Changelog Page
- Show entries where:
  - `is_active = true`
  - `audience in (public, both)`
  - `published_at <= now`
- Simple readable timeline/cards (title, date, summary)
- No admin-only controls

## Commit Link Handling
- Commit links are primarily for admins.
- Admin detail/list view should render clickable commit links.
- Public view may hide commit links by default (recommended).

## Query Rules
### Admin visibility
- Entries with audience `admin` or `both`
- optionally also show `public` entries if desired in manager overview

### Public visibility
- Entries with audience `public` or `both`
- only active + published

## Migration Strategy
1. Create `changelog_entries` table.
2. Add `last_seen_changelog_version` to `users`.
3. Add admin/public navigation links.
4. Add admin manager UI.
5. Add public changelog page.
6. Add admin one-time modal logic.

## Acceptance Criteria
- Admin has permanent menu access to changelog.
- Public has menu access to `Was ist neu`.
- Admin sees one-time modal after login when unseen updates exist.
- Confirming modal marks latest version as seen for that admin.
- Admin can create entries with optional GitHub commit links.
- Commit links are shown in admin changelog view.
- Public sees only public/both active published entries.

## Out of Scope
- Auto-generating changelog entries directly from git history.
- Full markdown renderer with complex embeds.
- Per-entry read tracking for public visitors.

## Notes
- Run migrations manually:
  - `php artisan migrate`
