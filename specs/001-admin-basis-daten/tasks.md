---

description: "Task list for admin panel feature implementation"

---

# Tasks: Admin-Bereich für Basis-Daten-Verwaltung

**Input**: Design documents from `/specs/001-admin-basis-daten/`
**Prerequisites**: plan.md (✅ complete), spec.md (✅ complete), data-model.md (✅ complete), contracts/openapi.yaml (✅ complete)

**Tests**: Not explicitly requested in spec - tests are OPTIONAL. Developers should follow TDD best practices.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Include exact file paths in descriptions

## Path Conventions

- **Backend**: `app/`, `database/migrations/`, `routes/`, `resources/`
- **Frontend**: `resources/js/`, `resources/views/`, `resources/css/`
- **Database**: `database/migrations/`, `database/seeders/`
- All paths are absolute from project root

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Laravel 12 project initialization and basic structure

- [ ] T001 Initialize Laravel 12 project with composer (create project or existing setup)
- [ ] T002 [P] Copy .env.example to .env and configure database settings (DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE)
- [ ] T003 [P] Generate APP_KEY via `php artisan key:generate`
- [ ] T004 [P] Configure Sanctum authentication in config/sanctum.php (SANCTUM_STATEFUL_DOMAINS, SESSION_DRIVER)
- [ ] T005 [P] Install frontend dependencies: `npm install` (Vite, TailwindCSS, DaisyUI, Mary UI)
- [ ] T006 Configure Vite in vite.config.js for Blade template compilation and HMR
- [ ] T007 [P] Configure TailwindCSS in tailwind.config.js with DaisyUI plugin
- [ ] T008 [P] Create app/Http/Resources/ directory for API Resource classes
- [ ] T009 [P] Create app/Http/Requests/ directory for Form Request validation classes

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core infrastructure that MUST be complete before ANY user story can be implemented

**⚠️ CRITICAL**: No user story work can begin until this phase is complete

- [ ] T010 Create database migration: `database/migrations/2025_01_XX_create_users_table.php` with all fields (name, email, password, role, is_active, last_login_at, timestamps)
- [ ] T011 [P] Create User model in `app/Models/User.php` with relationships to all entities and Sanctum trait
- [ ] T012 [P] Create UserController in `app/Http/Controllers/Auth/AuthController.php` with login/logout endpoints
- [ ] T013 [P] Create UserLoginRequest Form Request in `app/Http/Requests/UserLoginRequest.php` with email/password validation
- [ ] T014 [P] Create UserResource in `app/Http/Resources/UserResource.php` for API responses
- [ ] T015 Create auth routes in `routes/api.php`: POST /auth/login, POST /auth/logout with Sanctum middleware
- [ ] T016 [P] Create base service class structure in `app/Services/BaseService.php` (optional but recommended)
- [ ] T017 [P] Create exception handling middleware for API in `app/Http/Middleware/HandleApiExceptions.php`
- [ ] T018 Create admin middleware in `app/Http/Middleware/IsAdmin.php` to verify admin role
- [ ] T019 [P] Create database seeders directory structure: `database/seeders/`
- [ ] T020 Create UserSeeder in `database/seeders/UserSeeder.php` to seed test admin users

**Checkpoint**: Foundation ready - user story implementation can now begin in parallel

---

## Phase 3: User Story 2 - Admin verwaltet Lebensarten (Priority: P2) 🎯 MVP Foundation

**Goal**: Create LifeForm management - required dependency for User Story 4 (Plants)

**Why P2 before P1**: Foundational catalog needed before complex plant data entry

**Independent Test**: Admin can create life forms (Baum, Strauch, Kraut, Gras, Farn, etc.) and these appear as dropdowns in plant forms

### Database & Models for User Story 2

- [ ] T021 Create migration: `database/migrations/2025_01_XX_create_life_forms_table.php` (id, user_id FK, name unique, description, timestamps)
- [ ] T022 Create LifeForm model in `app/Models/LifeForm.php` with relationships to User and Plant
- [ ] T023 [P] Create LifeFormRequest Form Request in `app/Http/Requests/LifeFormRequest.php` (name: required|unique, description: nullable)

### API & Services for User Story 2

- [ ] T024 [P] Create LifeFormService in `app/Services/LifeFormService.php` with CRUD methods
- [ ] T025 [P] Create LifeFormResource in `app/Http/Resources/LifeFormResource.php` for API responses
- [ ] T026 Create LifeFormController in `app/Http/Controllers/LifeFormController.php` with index/store/show/update/delete methods
- [ ] T027 Create API routes in `routes/api.php` for /life-forms (all CRUD endpoints with Sanctum auth)

### Frontend for User Story 2

- [ ] T028 [P] Create LifeFormManager component in `resources/js/components/LifeFormManager.vue` (list, create, edit, delete forms)
- [ ] T029 [P] Create Axios API service in `resources/js/services/lifeFormApi.js` for life form endpoints
- [ ] T030 Create admin navigation in `resources/js/components/AdminNavigation.vue` with link to Life Forms manager

**Checkpoint**: Life Forms management complete and testable independently

---

## Phase 4: User Story 5 - Admin verwaltet Verbreitungsgebiete (Priority: P2)

**Goal**: Create DistributionArea management - required dependency for User Story 1 (Species)

**Why P2 before P1**: Foundational catalog needed before complex species data entry

**Independent Test**: Admin can create distribution areas (z.B. "Mitteleuropa", "Südostasien") and these appear as multi-select in species forms

### Database & Models for User Story 5

- [ ] T031 Create migration: `database/migrations/2025_01_XX_create_distribution_areas_table.php` (id, user_id FK, name unique, description, timestamps)
- [ ] T032 Create DistributionArea model in `app/Models/DistributionArea.php` with M2M relationship to Species
- [ ] T033 [P] Create DistributionAreaRequest Form Request in `app/Http/Requests/DistributionAreaRequest.php` (name: required|unique, description: nullable)

### API & Services for User Story 5

- [ ] T034 [P] Create DistributionAreaService in `app/Services/DistributionAreaService.php` with CRUD methods
- [ ] T035 [P] Create DistributionAreaResource in `app/Http/Resources/DistributionAreaResource.php` for API responses
- [ ] T036 Create DistributionAreaController in `app/Http/Controllers/DistributionAreaController.php` with index/store/show/update/delete methods
- [ ] T037 Create API routes in `routes/api.php` for /distribution-areas (all CRUD endpoints with Sanctum auth)

### Frontend for User Story 5

- [ ] T038 [P] Create DistributionAreaManager component in `resources/js/components/DistributionAreaManager.vue` (list, create, edit, delete forms)
- [ ] T039 [P] Create Axios API service in `resources/js/services/distributionAreaApi.js` for distribution area endpoints
- [ ] T040 Update AdminNavigation component with link to Distribution Areas manager

**Checkpoint**: Distribution Areas management complete and testable independently

---

## Phase 5: User Story 3 - Admin verwaltet Habitate (Priority: P2)

**Goal**: Create hierarchical Habitat management - required dependency for User Stories 1 & 4 (Species & Plants)

**Why P2 before P1**: Foundational hierarchical catalog needed before complex species/plant data entry

**Independent Test**: Admin can create habitats with hierarchy (Wald → Laubwald, Wald → Nadelwald, Ruderalflächen → Wegrand) and these appear in species/plant forms

### Database & Models for User Story 3

- [ ] T041 Create migration: `database/migrations/2025_01_XX_create_habitats_table.php` (id, user_id FK, parent_id FK self-reference, name, description, level INT, timestamps)
- [ ] T042 Create Habitat model in `app/Models/Habitat.php` with self-referencing parent relationship and children relationship, M2M to Species and Plant
- [ ] T043 [P] Create HabitatRequest Form Request in `app/Http/Requests/HabitatRequest.php` (name: required|string, parent_id: nullable|exists, level: required|in:1,2)

### API & Services for User Story 3

- [ ] T044 [P] Create HabitatService in `app/Services/HabitatService.php` with CRUD methods and hierarchy handling (prevent circular references)
- [ ] T045 [P] Create HabitatResource in `app/Http/Resources/HabitatResource.php` including children array for hierarchical display
- [ ] T046 Create HabitatController in `app/Http/Controllers/HabitatController.php` with index/store/show/update/delete methods
- [ ] T047 Create API routes in `routes/api.php` for /habitats with query params for parent_id and level filtering

### Frontend for User Story 3

- [ ] T048 [P] Create HabitatManager component in `resources/js/components/HabitatManager.vue` with hierarchical tree display and create/edit/delete for both levels
- [ ] T049 [P] Create HabitatTreeView component in `resources/js/components/HabitatTreeView.vue` for rendering hierarchical structure
- [ ] T050 [P] Create Axios API service in `resources/js/services/habitatApi.js` for habitat endpoints
- [ ] T051 Update AdminNavigation component with link to Habitat manager

**Checkpoint**: Habitat management with hierarchy complete and testable independently

---

## Phase 6: User Story 2 (Continuation) - Admin verwaltet Familien (Priority: P2)

**Goal**: Create Family management - required dependency for User Story 1 (Species)

**Why in Phase 6**: Depends on Phase 2 foundation complete

**Independent Test**: Admin can create families (z.B. "Nymphalidae", "Pieridae") and these appear as dropdown in species forms. Delete protection works when family has species.

### Database & Models for User Story 2

- [ ] T052 Create migration: `database/migrations/2025_01_XX_create_families_table.php` (id, user_id FK, name unique, description, timestamps)
- [ ] T053 Create Family model in `app/Models/Family.php` with relationships to User and Species
- [ ] T054 [P] Create FamilyRequest Form Request in `app/Http/Requests/FamilyRequest.php` (name: required|unique, description: nullable)

### API & Services for User Story 2

- [ ] T055 [P] Create FamilyService in `app/Services/FamilyService.php` with CRUD methods and delete protection (check species usage)
- [ ] T056 [P] Create FamilyResource in `app/Http/Resources/FamilyResource.php` for API responses
- [ ] T057 Create FamilyController in `app/Http/Controllers/FamilyController.php` with index/store/show/update/delete methods
- [ ] T058 Create API routes in `routes/api.php` for /families (all CRUD endpoints with Sanctum auth)
- [ ] T059 [P] Create FamilyService method `checkCanDelete()` to prevent deletion if species exist with that family

### Frontend for User Story 2

- [ ] T060 [P] Create FamilyManager component in `resources/js/components/FamilyManager.vue` (list, create, edit, delete forms with delete confirmation)
- [ ] T061 [P] Create Axios API service in `resources/js/services/familyApi.js` for family endpoints
- [ ] T062 Update AdminNavigation component with link to Family manager

**Checkpoint**: Family management complete and testable independently

---

## Phase 7: User Story 4 - Admin verwaltet Pflanzen (Priority: P2) 🌱

**Goal**: Create comprehensive Plant management with botanical attributes

**Dependencies**: Requires Phase 3 (LifeForms), Phase 5 (Habitats) complete

**Independent Test**: Admin can create plants with all botanical attributes (light 1-9, temp 1-9, etc.) and native/invasive flags. Plants appear in species forms as available host plants.

### Database & Models for User Story 4

- [ ] T063 Create migration: `database/migrations/2025_01_XX_create_plants_table.php` (id, user_id FK, life_form_id FK, name, scientific_name, family_genus, light_number, temperature_number, continentality_number, reaction_number, moisture_number, moisture_variation, nitrogen_number, bloom_months JSON, bloom_color, plant_height_cm, lifespan ENUM, location, is_native BOOL, is_invasive BOOL, threat_status, persistence_organs, timestamps)
- [ ] T064 Create Plant model in `app/Models/Plant.php` with relationships to User, LifeForm, M2M to Habitat and Species
- [ ] T065 [P] Create PlantRequest Form Request in `app/Http/Requests/PlantRequest.php` with validation for all fields (ecological scales 1-9, lifespan enum, etc.)
- [ ] T066 [P] Create pivot table migration: `database/migrations/2025_01_XX_create_plant_habitat_table.php` (plant_id FK, habitat_id FK, timestamps)

### API & Services for User Story 4

- [ ] T067 [P] Create PlantService in `app/Services/PlantService.php` with CRUD methods and filter methods (by ecological values, native status, etc.)
- [ ] T068 [P] Create PlantResource in `app/Http/Resources/PlantResource.php` including habitats array for display
- [ ] T069 Create PlantController in `app/Http/Controllers/PlantController.php` with index (with filters)/store/show/update/delete methods
- [ ] T070 Create API routes in `routes/api.php` for /plants with query params for filtering (is_native, is_invasive, light_number, etc.)

### Frontend for User Story 4

- [ ] T071 [P] Create PlantForm component in `resources/js/components/PlantForm.vue` with form fields for all plant attributes
- [ ] T072 [P] Create EcologicalScaleInput component in `resources/js/components/EcologicalScaleInput.vue` (slider/input for 1-9 scales)
- [ ] T073 [P] Create PlantHabitatSelector component in `resources/js/components/PlantHabitatSelector.vue` for multi-select habitat assignment
- [ ] T074 [P] Create PlantList component in `resources/js/components/PlantList.vue` with filter options and pagination
- [ ] T075 [P] Create Axios API service in `resources/js/services/plantApi.js` for plant endpoints
- [ ] T076 Update AdminNavigation component with link to Plant manager

**Checkpoint**: Plant management with all botanical attributes complete and testable independently

---

## Phase 8: User Story 1 - Admin erstellt neue Schmetterlingsart (Priority: P1) 🦋 MVP

**Goal**: Complete species management with all attributes - the MVP-critical feature

**Dependencies**: Requires Phase 2 (Foundation), Phase 6 (Families), Phase 5 (Distributions), Phase 3 (Habitats), Phase 4 (Plants) complete

**Independent Test**: Admin can create species with all attributes including multiple generations. Each generation stores different flight months. Species appears in list and is retrievable with all data.

### Database & Models for User Story 1

- [ ] T077 Create migration: `database/migrations/2025_01_XX_create_species_table.php` (id, user_id FK, family_id FK, name, scientific_name, size_category ENUM, color_description, special_features, gender_differences, generations_per_year INT, hibernation_stage ENUM, pupal_duration_days INT, red_list_status_de, red_list_status_eu, abundance_trend, protection_status ENUM, timestamps)
- [ ] T078 Create Species model in `app/Models/Species.php` with relationships to User, Family, M2M to DistributionArea, Habitat, Plant
- [ ] T079 [P] Create SpeciesRequest Form Request in `app/Http/Requests/SpeciesRequest.php` with validation for all fields (size_category enum, generations_per_year 1-4, hibernation_stage enum, etc.)
- [ ] T080 [P] Create pivot table migration: `database/migrations/2025_01_XX_create_species_distribution_table.php` (species_id FK, distribution_area_id FK, timestamps)
- [ ] T081 [P] Create pivot table migration: `database/migrations/2025_01_XX_create_species_habitat_table.php` (species_id FK, habitat_id FK, timestamps)
- [ ] T082 [P] Create pivot table migration: `database/migrations/2025_01_XX_create_species_plant_table.php` (species_id FK, plant_id FK, plant_type VARCHAR, timestamps)

### Database Seeders for User Story 1

- [ ] T083 [P] Create DistributionAreaSeeder in `database/seeders/DistributionAreaSeeder.php` with sample areas (Mitteleuropa, Südwesteuropa, etc.)
- [ ] T084 [P] Create LifeFormSeeder in `database/seeders/LifeFormSeeder.php` with sample life forms (Baum, Strauch, Kraut, Gras, Farn)
- [ ] T085 [P] Create HabitatSeeder in `database/seeders/HabitatSeeder.php` with hierarchical sample habitats (Wald+children, Ruderalflächen+children)
- [ ] T086 [P] Create FamilySeeder in `database/seeders/FamilySeeder.php` with sample butterfly families (Nymphalidae, Pieridae, Lycaenidae, etc.)

### API & Services for User Story 1

- [ ] T087 [P] Create SpeciesService in `app/Services/SpeciesService.php` with CRUD methods, generation handling, and filter methods (by family, size, generation count, etc.)
- [ ] T088 [P] Create SpeciesResource in `app/Http/Resources/SpeciesResource.php` including nested family, distributions, habitats, host_plants arrays
- [ ] T089 Create SpeciesController in `app/Http/Controllers/SpeciesController.php` with index (with filters)/store/show/update/delete methods
- [ ] T090 Create API routes in `routes/api.php` for /species with query params for filtering (family_id, size_category, generations_per_year, etc.)

### Frontend for User Story 1

- [ ] T091 [P] Create SpeciesForm component in `resources/js/components/SpeciesForm.vue` with multi-step form for all species attributes
- [ ] T092 [P] Create SizeCategorySelect component in `resources/js/components/SizeCategorySelect.vue` (XS/S/M/L/XL dropdown)
- [ ] T093 [P] Create GenerationInput component in `resources/js/components/GenerationInput.vue` for entering multiple generations with flight months
- [ ] T094 [P] Create DistributionAreaSelector component in `resources/js/components/DistributionAreaSelector.vue` for multi-select distribution areas
- [ ] T095 [P] Create HabitatSelector component in `resources/js/components/HabitatSelector.vue` for multi-select habitats
- [ ] T096 [P] Create HostPlantSelector component in `resources/js/components/HostPlantSelector.vue` for multi-select host plants
- [ ] T097 [P] Create SpeciesList component in `resources/js/components/SpeciesList.vue` with comprehensive filtering and pagination
- [ ] T098 [P] Create Axios API service in `resources/js/services/speciesApi.js` for species endpoints
- [ ] T099 [P] Create AdminPanel.vue in `resources/js/pages/AdminPanel.vue` as main admin interface with router/navigation
- [ ] T100 Update AdminNavigation component with link to Species manager

### Migrations & Database Setup for User Story 1

- [ ] T101 Run all migrations: `php artisan migrate` (creates all tables with proper constraints and indexes)
- [ ] T102 [P] Run seeders: `php artisan db:seed` (populates test data for dropdowns)

**Checkpoint**: Species management complete with all attributes - MVP core functionality working!

---

## Phase 9: Polish & Cross-Cutting Concerns

**Purpose**: Improvements that affect multiple user stories

- [ ] T103 [P] Create error handling component `resources/js/components/ErrorAlert.vue` for API errors
- [ ] T104 [P] Create loading indicator component `resources/js/components/LoadingSpinner.vue` for async operations
- [ ] T105 [P] Create pagination component `resources/js/components/Pagination.vue` for list views
- [ ] T106 [P] Create confirmation dialog component `resources/js/components/ConfirmDialog.vue` for delete operations
- [ ] T107 [P] Add global error handler in `resources/js/app.js` for all API calls
- [ ] T108 [P] Add success notifications in all forms (create/update/delete) via toast or alert component
- [ ] T109 [P] Add form reset functionality after successful submissions
- [ ] T110 Update CSS in `resources/css/app.css` to import TailwindCSS and DaisyUI styles
- [ ] T111 Create base layout in `resources/views/app.blade.php` as SPA root with <div id="app">
- [ ] T112 [P] Add input debouncing for search/filter fields to reduce API calls
- [ ] T113 [P] Implement optimistic UI updates (show changes before server confirms)
- [ ] T114 [P] Add form validation feedback (inline error messages on blur)
- [ ] T115 Run quickstart.md validation: test complete setup flow from fresh install
- [ ] T116 [P] Update project README.md with setup instructions and quick start
- [ ] T117 [P] Add API response logging in development mode for debugging
- [ ] T118 [P] Configure CORS properly for local development (localhost:3000 to localhost:8000)

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies - can start immediately
- **Foundational (Phase 2)**: Depends on Setup completion - BLOCKS all user stories
- **Phase 3 (LifeForms, US2)**: Depends on Foundational completion
- **Phase 4 (DistributionAreas, US5)**: Depends on Foundational completion
- **Phase 5 (Habitats, US3)**: Depends on Foundational completion
- **Phase 6 (Families, US2)**: Depends on Foundational completion
- **Phase 7 (Plants, US4)**: Depends on Phase 3 (LifeForms) and Phase 5 (Habitats)
- **Phase 8 (Species, US1)**: Depends on Phases 3, 4, 5, 6 (all base entities) - MVP Core feature
- **Phase 9 (Polish)**: Depends on Phases 3-8 - can start parallel with Phase 8

### Parallel Opportunities

**Phase 1 Setup**: All tasks marked [P] can run in parallel (independent file creation)

**Phase 2 Foundation**: All tasks marked [P] can run in parallel (independent models, migrations, services)

**Phase 3-6 Base Entities** (LifeForms, Distributions, Habitats, Families):
- Database migrations can run in any order (all independent)
- Models can be created in parallel
- Services can be created in parallel
- Controllers can be created in parallel
- Frontend components can be created in parallel
- These 4 phases are MOSTLY independent and can run in parallel

**Phase 8 Species Implementation**:
- Database migrations for species tables can run in parallel (T077-T082)
- Seeders can run in parallel (T083-T086)
- Services can be created in parallel (T087)
- Frontend components can be created in parallel (T091-T099)

**Phase 9 Polish**: All tasks marked [P] can run in parallel (independent components and utilities)

---

## Implementation Strategy

### MVP First (User Story 1 + Dependencies Only)

**Fastest path to working admin panel with species management:**

1. Complete Phase 1: Setup ✅
2. Complete Phase 2: Foundational ✅
3. Complete Phase 3: LifeForms (required for Plants) ✅
4. Complete Phase 4: DistributionAreas (required for Species) ✅
5. Complete Phase 5: Habitats (required for Species) ✅
6. Complete Phase 6: Families (required for Species) ✅
7. Complete Phase 7: Plants (required for Species) ✅
8. Complete Phase 8: Species - **STOP HERE FOR MVP**
9. **VALIDATE**: Test User Story 1 independently
   - Create species with multiple generations
   - Verify data persistence and retrieval
   - Test form validation
   - Test multi-select relationships (families, habitats, distributions, plants)
10. Deploy/Demo MVP ✅

### Incremental Delivery (Full Feature)

After MVP, add remaining features:

1. MVP complete (through Phase 8)
2. Run Phase 9 Polish & Cross-Cutting Concerns
3. User test the complete admin panel
4. Deploy/Demo Complete Feature ✅

### Parallel Team Strategy (If Multiple Developers)

**With 3-4 developers:**

1. **Team**: Complete Phase 1 Setup together ✅
2. **Team**: Complete Phase 2 Foundational together ✅
3. **Developer A**: Phase 3 (LifeForms) + Phase 4 (DistributionAreas)
4. **Developer B**: Phase 5 (Habitats) + Phase 6 (Families)
5. **Developer C**: Phase 7 (Plants) - waits for A & B
6. **Team**: Phase 8 (Species) - can distribute components
7. **Developer D** (if available): Phase 9 Polish in parallel with Phase 8

---

## Testing Strategy

**Manual Testing** (recommended for MVP):
- Test each user story independently after completion
- Use Postman/Insomnia for API testing
- Manual UI testing in browser
- Test form validation with invalid data
- Test delete protection (can't delete entity with dependencies)

**Automated Testing** (optional enhancements):
- Unit tests for services (CRUD logic, validation)
- Integration tests for API endpoints
- Feature tests for complete user journeys
- Can be added after MVP if needed

---

## Notes

- [P] tasks = different files, no dependencies between them
- [Story] label maps task to specific user story for traceability
- Each user story should be independently completable and testable
- Tasks progress from database → models → services → controllers → routes → frontend
- Migrations must run in order (database/migrations/ ordered by filename/timestamp)
- Seeders populate test data for dropdown selections
- Each phase ends with "Checkpoint" showing independent testability
- MVP scope: Phase 1 + Phase 2 + Phase 3 + Phase 4 + Phase 5 + Phase 6 + Phase 7 + Phase 8
- Complete Feature scope: Phase 1 through Phase 9
- Stop at any checkpoint to validate story independently
- Avoid: vague tasks, same file conflicts, cross-story dependencies that break independence

---

## Quick Reference: Task Count Summary

| Phase | Title | Task Count | Parallels |
|-------|-------|-----------|-----------|
| Phase 1 | Setup | 9 tasks | T002-T009 (8 parallel) |
| Phase 2 | Foundational | 11 tasks | T011-T020 (10 parallel) |
| Phase 3 | LifeForms (US2) | 10 tasks | T022-T030 (8 parallel) |
| Phase 4 | DistributionAreas (US5) | 10 tasks | T032-T040 (8 parallel) |
| Phase 5 | Habitats (US3) | 13 tasks | T042-T051 (9 parallel) |
| Phase 6 | Families (US2) | 11 tasks | T053-T062 (8 parallel) |
| Phase 7 | Plants (US4) | 16 tasks | T065-T076 (13 parallel) |
| Phase 8 | Species (US1) | 24 tasks | T079-T102 (20 parallel) |
| Phase 9 | Polish | 16 tasks | T103-T118 (13 parallel) |
| | **TOTAL** | **120 tasks** | **~95 parallelizable** |

