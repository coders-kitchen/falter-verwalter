# Implementation Plan: Admin-Bereich für Basis-Daten-Verwaltung

**Branch**: `001-admin-basis-daten` | **Date**: 2025-11-02 | **Spec**: [spec.md](spec.md)
**Input**: Feature specification from `/specs/001-admin-basis-daten/spec.md`

**Note**: This template is filled in by the `/speckit.plan` command. See `.specify/templates/commands/plan.md` for the execution workflow.

## Summary

Admin-Panel zur Verwaltung von Basis-Daten für die Falter-Verwalter App. Die Implementierung umfasst 6 User Stories mit CRUD-Funktionen für:
- Schmetterlingsarten mit umfangreichen morphologischen, ökologischen und Lebenszyklusdaten
- Schmetterlingsarten-Familien (taxonomisch)
- Lebensräume (Habitate) mit hierarchischer Struktur
- Futter- und Raupenpflanzen mit botanischen Zeigerwerten
- Pflanzen-Lebensarten (Baum, Strauch, Kraut, etc.)
- Verbreitungsgebiete

Der Admin-Bereich ist das MVP-Fundament: Ohne Datenerfassung kann keine andere App-Funktionalität existieren.

## Technical Context

**Language/Version**: PHP 8.2+ / Laravel 12 ✅
**Primary Dependencies**: Laravel 12 Framework, Eloquent ORM, Sanctum Authentication ✅
**Storage**: MySQL 8+ ✅
**Testing**: PHPUnit + Pest ✅
**Target Platform**: Web-Anwendung (Browser) | Admin-Interface
**Project Type**: Web application (Frontend + Backend) - Single Page Application mit API
**Performance Goals**: Admin-Panel muss mit bis zu 1000 Schmetterlingsarten ohne spürbare Verzögerung arbeiten; Formularvalidierung < 500ms
**Constraints**: Response Times < 500ms für normale Abfragen, < 2s für komplexe Suchen; Benutzerfreundlichkeit für Laien und Fachleute
**Scale/Scope**: MVP mit 6 Data-Management Screens, ~20 Formulare, ~8 Entitäten mit Relationships

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

### Constitutional Principles Alignment:

✅ **I. Benutzerfreundlichkeit**:
- Admin-Formulare werden intuitiv gestaltet mit klarer Navigation und Hilfetext
- Größenkategorien statt Zahleneingabe für Benutzerfreundlichkeit
- Ökologische Zeigerwerte als standardisierte 1-9 Skalen für einfache Erfassung

✅ **II. Datenintegrität**:
- Validierung auf Eingabeebene (Pflichtfelder, Datentypen, Bereiche 1-9)
- Referenzialintegrität-Schutz (Verhinderung von Löschung bei Abhängigkeiten)
- Hierarchische Struktur für Habitate und Taxonomie unterstützt Konsistenz

✅ **III. Wartbarkeit**:
- Klare Entity-Relationships mit Fremdschlüsseln
- Separate Verwaltung von Basis-Katalogen (Familien, Habitate, Lebensarten)
- API-basierte Architektur ermöglicht Entkopplung

✅ **IV. Suchbarkeit & Filterung**:
- Alle Basis-Daten müssen durchsuchbar sein (Paginierung, Filter)
- Ökologische Zeigerwerte ermöglichen später komplexe Filterung
- Hierarchische Habitate unterstützen hierarchische Navigation

✅ **V. Dokumentation**:
- Formulare mit Hilfetext und Tooltips
- API-Contracts dokumentiert in OpenAPI-Format
- Quickstart-Guide für Admin-Benutzer

✅ **Sicherheit & Datenschutz**:
- Admin-Authentifizierung und Autorisierung erforderlich
- Audit Trail für Änderungen
- Datenverschlüsselung in Transit (HTTPS)

✅ **Performance & Skalierung**:
- Indexierung auf häufig abgerufenen Feldern
- Paginierung für große Datenmengen
- Response Times < 500ms für normale Abfragen

**GATE STATUS**: ✅ PASSED - Alle Prinzipien berücksichtigt

## Project Structure

### Documentation (this feature)

```text
specs/[###-feature]/
├── plan.md              # This file (/speckit.plan command output)
├── research.md          # Phase 0 output (/speckit.plan command)
├── data-model.md        # Phase 1 output (/speckit.plan command)
├── quickstart.md        # Phase 1 output (/speckit.plan command)
├── contracts/           # Phase 1 output (/speckit.plan command)
└── tasks.md             # Phase 2 output (/speckit.tasks command - NOT created by /speckit.plan)
```

### Source Code (repository root)

```text
backend/
├── src/
│   ├── models/
│   │   ├── species.py         # Schmetterlingsart Entity
│   │   ├── family.py          # Familie Entity
│   │   ├── habitat.py         # Habitat Entity (mit Hierarchie)
│   │   ├── plant.py           # Pflanze Entity
│   │   ├── life_form.py       # Lebensart Entity
│   │   └── distribution.py    # Verbreitungsgebiet Entity
│   ├── services/
│   │   ├── species_service.py
│   │   ├── family_service.py
│   │   ├── habitat_service.py
│   │   ├── plant_service.py
│   │   ├── life_form_service.py
│   │   └── distribution_service.py
│   └── api/
│       ├── routes.py          # Alle CRUD Endpoints
│       ├── schemas.py         # Request/Response Schemas
│       └── middleware/
│           └── auth.py        # Admin-Authentifizierung
└── tests/
    ├── contract/              # API Contract Tests
    ├── integration/           # Datenbank + API Tests
    └── unit/                  # Service Unit Tests

frontend/
├── src/
│   ├── components/
│   │   ├── SpeciesForm.tsx        # Schmetterlingsart CRUD
│   │   ├── FamilyManager.tsx      # Familie CRUD
│   │   ├── HabitatManager.tsx     # Habitat CRUD
│   │   ├── PlantForm.tsx          # Pflanze CRUD
│   │   ├── LifeFormManager.tsx    # Lebensart CRUD
│   │   ├── DistributionManager.tsx # Verbreitungsgebiet CRUD
│   │   └── shared/
│   │       ├── FormField.tsx
│   │       ├── ValidationMessage.tsx
│   │       └── EcologicalScaleInput.tsx
│   ├── pages/
│   │   └── AdminPanel.tsx         # Hauptseite
│   └── services/
│       ├── api.ts
│       └── auth.ts
└── tests/
    ├── components/
    └── integration/
```

**Structure Decision**: Web application mit separaten Backend (API) und Frontend (SPA). Backend verwaltet Daten und Geschäftslogik, Frontend ist Benutzeroberfläche für Admin-Panel. Ermöglicht späteren Zugriff durch andere Client-Anwendungen (z.B. Mobile App, Public Search Interface).

## Complexity Tracking

> No violations found - Constitution Check PASSED ✅

---

## Phase 1 Completion Report

### ✅ Phase 1 Deliverables (100% Complete)

1. **data-model.md** ✅
   - 7 Main Entities with full Eloquent specifications
   - 4 Pivot tables for Many-to-Many relationships
   - Complete validation rules
   - Performance indexes documented
   - Migration strategy with execution order

2. **contracts/openapi.yaml** ✅
   - Complete REST API specification
   - 6 resource endpoints with CRUD operations
   - Authentication (Sanctum) documented
   - Request/Response schemas for all endpoints
   - Error handling and validation responses

3. **quickstart.md** ✅
   - Installation and setup instructions
   - Database seeding guide
   - Development server startup procedures
   - Project structure documentation
   - API routes reference
   - Frontend component examples
   - Production deployment checklist
   - Troubleshooting guide

### Technology Stack - Final Decisions

| Component | Decision | Rationale |
|-----------|----------|-----------|
| Language/Framework | PHP 8.2+ / Laravel 12 | Full-featured MVC, built-in validation, Eloquent ORM. Längere Support bis Feb 2027. Minimal breaking changes vs Laravel 11 |
| ORM | Eloquent | Intuitive relationships, excellent for hierarchies |
| Authentication | Laravel Sanctum | SPA-optimized, simple setup, HttpOnly cookies |
| Database | MySQL 8+ | Robust, performant, good indexing for medium scale |
| Frontend | Vite + TailwindCSS + DaisyUI + Mary UI | Fast development, pre-built components, responsive |
| Testing | PHPUnit + Pest | Built-in, powerful, elegant syntax option |

---

## Architecture Highlights

### Backend Architecture
- **Separation of Concerns**: Controllers → Services → Models → Database
- **API-First Design**: RESTful endpoints with validation at multiple layers
- **Eloquent Relationships**: Leverages ORM capabilities for complex M2M queries
- **Form Requests**: Built-in validation and error handling

### Frontend Architecture
- **Component-Based**: Vue/Alpine for interactive forms
- **TailwindCSS**: Utility-first CSS, minimal custom styling
- **DaisyUI + Mary UI**: Pre-built form components aligned with design
- **Axios Client**: Centralized API communication with error handling

### Database Design
- **Hierarchical Habitats**: Self-referencing FK for parent-child relationships
- **M2M Flexibility**: Pivot tables allow complex species-habitat-plant relationships
- **Indexing Strategy**: Optimized for common queries (filters by category, ecological values)
- **Referential Integrity**: Prevents orphaned data, enforces constraints

---

## Constitution Alignment - Phase 1 Validation

✅ **Benutzerfreundlichkeit**:
- Size categories (XS-XL) instead of numeric values
- Ecological scales (1-9) standardized and documented
- Form validation provides clear error messages
- Component examples show UX best practices

✅ **Datenintegrität**:
- Validation rules in Form Requests
- Foreign key constraints in migrations
- Unique constraints for base entities
- Referential integrity checks documented

✅ **Wartbarkeit**:
- Clean separation of concerns (Models, Controllers, Services)
- Eloquent relationships reduce boilerplate
- Consistent naming conventions
- Well-documented code structure

✅ **Suchbarkeit**:
- Pagination strategy documented
- Filtering parameters in API spec
- Indexes defined for performance
- Query optimization guidelines

✅ **Dokumentation**:
- OpenAPI spec for API documentation
- Quickstart guide for developers
- Entity descriptions in data-model
- Code examples in quickstart

---

## Known Limitations & Future Enhancements

**Current MVP Scope**:
- Admin-panel only (not public-facing)
- Single admin user role (admin/viewer)
- MySQL database (not multi-database support)
- English/German UI (localization can be added later)

**Future Enhancement Opportunities**:
- Advanced filtering and saved searches
- Bulk import/export features
- Photo gallery integration for species
- Mobile app API optimization
- GraphQL endpoint (alongside REST)
- Real-time collaboration features

---

## Next Phase: Implementation (Phase 2)

**Phase 2 will generate:**
- tasks.md: Actionable implementation tasks organized by user story
- Code scaffolds for Controllers, Models, Migrations
- Frontend component templates
- Test stubs for TDD

**Expected Duration**: 3-4 weeks for MVP (depending on team size)
