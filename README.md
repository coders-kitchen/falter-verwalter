# Falter Verwalter v2

Admin-managed Laravel application for managing butterfly and plant data, plus a public website for discovery.

## Project Purpose

`Falter Verwalter` is a biodiversity information system with two audiences:

- `Admins`: manage species, plants, habitats, families, threat categories, distribution areas, users, and related mappings.
- `Visitors (public)`: browse species and plants, discover butterflies by selected plants, inspect life-cycle and distribution information.

Current endangered/distribution model:

- Endangerment is modeled via `threat_categories`.
- Geography is modeled via `distribution_areas`.
- The mapping table/model `species_distribution_areas` links species to areas and optional threat category/status.

## Tech Stack

- PHP `8.2+`
- Laravel `12`
- Livewire + Volt + Flux
- Blade + Tailwind + DaisyUI
- Pest for tests

## Authentication

This project currently uses `custom auth-only`:

- login/logout handled by `AuthController`
- no Fortify flow (registration, reset-password, 2FA, profile pages are intentionally removed)

## Local Setup

1. Install dependencies:
```bash
composer install
npm install
```

2. Environment:
```bash
cp .env.example .env
php artisan key:generate
```

3. Database:
```bash
php artisan migrate
php artisan db:seed
```

4. Run app:
```bash
php artisan serve
npm run dev
```

## Testing

Run tests:
```bash
php artisan test
```

## Important Domain Notes

- `plants` now use:
  - `bloom_start_month` + `bloom_end_month` (instead of `bloom_months`)
  - `plant_height_cm_from` + `plant_height_cm_until` (instead of `plant_height_cm`)
- Active species endangerment/distribution logic uses:
  - `species_distribution_areas`
  - `distribution_areas`
  - `threat_categories`

## Archive: Regions Module

The old regions-based endangered model is archived and not active.

Archived path:

- `archive/regions/`

Contains archived code for:

- region model/pivot model
- region manager Livewire component
- admin/livewire region views
- region factory/seeder
- region unit test

### Why archived?

The project migrated from a `regions + species_region + conservation_status` concept to `distribution_areas + threat_categories + species_distribution_areas`.

### How to restore later

If regions are reintroduced in future:

1. Move needed files from `archive/regions/` back to original paths.
2. Re-enable routes and admin navigation entries for regions.
3. Re-add region seeding (`DatabaseSeeder`) if required.
4. Reconcile model relations/tests with the then-current domain model.

## Useful Paths

- Routes: `routes/web.php`
- Admin layout/nav: `resources/views/layouts/app.blade.php`
- Public pages: `resources/views/public/*`
- Livewire admin components: `app/Livewire/*`
- Livewire public components: `app/Livewire/Public/*`
- Core models: `app/Models/*`
- Migrations: `database/migrations/*`
- Tests: `tests/*`
