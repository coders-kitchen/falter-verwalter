# Research: Admin-Bereich für Basis-Daten-Verwaltung

**Date**: 2025-11-02
**Phase**: 0 - Pre-Design Research
**Status**: In Progress - Pending User Input

---

## Research Tasks & Findings

### 1. Technology Stack Selection

**Question**: Welche Programmiersprache, Web-Framework und Datenbank?

**Context**:
- Feature: Web-basiertes Admin-Panel mit CRUD für 6 Entitäten
- Scale: MVP, bis zu 1000 Schmetterlingsarten
- Performance: < 500ms Response Time für normale Abfragen
- Must-Have: Einfache Formularverwaltung, Validierung, API

**Decision**: ✅ RESOLVED

**Tech Stack Finalized**:
- **Backend Language/Framework**: PHP 8.2+ / **Laravel 12** ✅
- **Database**: **MySQL 8+** ✅
- **Frontend**: **Vite + TailwindCSS + DaisyUI + Mary UI** ✅
- **ORM**: **Eloquent** (Laravel's built-in ORM) ✅
- **Authentication**: **Laravel Sanctum** ✅

**Rationale**:
- **Laravel 12** (Released Feb 2025): ✅ Full-featured framework mit integrierter Authentifizierung, Validation, Middleware, Eloquent ORM. Längere Support bis Feb 2027. Minimal breaking changes vs Laravel 11.
- **Sanctum**: ✅ SPA-optimiert (HttpOnly Cookies), einfach zu konfigurieren, perfekt für Admin-Panel
- **Eloquent**: ✅ Intuitive API für komplexe Relationships (hierarchical Habitats, Many-to-Many)
- **MySQL**: ✅ Robust, performant für mittlere Datenmengen, gute Indizierung möglich
- **TailwindCSS + DaisyUI + Mary UI**: ✅ Schnelle UI-Entwicklung, responsive Design, fertige Komponenten
- **Vite**: ✅ Schneller Bundler, HMR für schnelle Entwicklung
- **PHP 8.2+**: ✅ Modernes PHP mit besserer Performance und Security

**Alternatives Considered**:
- Laravel 11: Ältere Version, Support endet Sept 2025 (früher als Laravel 12)
- Laravel Passport: Zu heavy für Sanctum-Anwendungsfall
- Session-basiert: Weniger Flexibilität für zukünftige Mobile/External Client Integration

**Version Decision Rationale**:
Laravel 12 bietet längere Support (bis Feb 2027 vs März 2026 bei Laravel 11), minimale Breaking Changes, und modernere PHP-Anforderungen. Perfekt für Langzeitprojekte.

---

### 2. Project Dependencies & Tools

**Question**: Zusätzliche Libraries und Tools?

**Decision**: ✅ RESOLVED

**Backend Dependencies (Laravel 12)**:
| Aspekt | Choice | Begründung |
|--------|--------|-----------|
| ORM | Eloquent (Built-in) | ✅ Perfekt für Laravel, intuitive API |
| Validation | Laravel Form Requests | ✅ Built-in, powerful Rule System |
| API Responses | laravel/framework (API Resources) | ✅ Built-in JSON transformation |
| Error Handling | Laravel Exception Handler | ✅ Centralized error handling |
| Testing | PHPUnit + Pest | ✅ Built-in + optional Pest für elegantere Tests |
| Database Migrations | Laravel Migrations | ✅ Built-in, versionskontrolliert |
| PHP Version | PHP 8.2+ | ✅ Modernes PHP mit besserer Performance |

**Frontend Dependencies (Vite + TailwindCSS)**:
| Aspekt | Choice | Begründung |
|--------|--------|-----------|
| CSS Framework | TailwindCSS v4 | ✅ Utility-first, klein Bundle Size |
| UI Components | DaisyUI + Mary UI | ✅ Fertige Components, TailwindCSS-basiert |
| Build Tool | Vite | ✅ Schnell, HMR, optimiert für SPAs |
| Form Handling | Alpine.js (optional) oder Vue 3 | ✅ Leichte Interaktivität, minimal JS |
| State Management | LocalStorage + API State | ✅ Einfach für Admin-Panel, wenig Overhead |
| HTTP Client | Axios | ✅ Simple API requests zu Laravel Backend |

**Additional Libraries**:
- **laravel/sanctum**: ✅ Authentication (bereits entschieden)
- **laravel/telescope** (Dev): ✅ Debugging und Development Tools
- **spatie/laravel-permission** (optional): ✅ Role-based Access Control wenn benötigt

---

### 3. Database Schema Strategy

**Research Finding**: ✅ RESOLVED

**Decision**: SQL-basierte relationale Datenbank mit folgenden Anforderungen:

**Entity-Relationship Model**:
```
Species (Schmetterlingsart)
├─ FK: family_id → Family
├─ M2M: distribution_areas → DistributionArea
├─ M2M: habitats → Habitat
└─ M2M: host_plants → Plant

Family (Familie)
└─ 1:N → Species

Habitat (Lebensraum)
├─ Parent: parent_id (selbstreferenziell für Hierarchie)
├─ M2M: species → Species
└─ M2M: plants → Plant

Plant (Pflanze)
├─ FK: life_form_id → LifeForm
├─ M2M: habitats → Habitat
└─ M2M: host_for_species → Species

LifeForm (Lebensart)
└─ 1:N → Plant

DistributionArea (Verbreitungsgebiet)
└─ M2M: species → Species
```

**Schema Features**:
- ✅ Hierarchische Habitate mit self-referencing parent_id
- ✅ Many-to-Many Relationships für flexible Zuordnungen
- ✅ Enum-ähnliche Tabellen für Größenkategorien und Überwinterungsstadium
- ✅ Indizes auf häufig gefilterten Feldern (lichtzahl, temperaturzahl, etc.)

---

### 4. API Design Strategy

**Research Finding**: ✅ RESOLVED

**Decision**: RESTful API mit folgender Struktur:

**Endpoints** (pro Entity):
```
GET    /api/admin/species              # Liste mit Pagination
GET    /api/admin/species/{id}         # Detail
POST   /api/admin/species              # Create
PUT    /api/admin/species/{id}         # Update
DELETE /api/admin/species/{id}         # Delete

GET    /api/admin/families
POST   /api/admin/families
... (für alle 6 Entitäten wiederholt)
```

**Request/Response Format**: JSON mit vollständiger Validierung

**Error Handling**:
- 400: Bad Request (Validierungsfehler)
- 401: Unauthorized
- 403: Forbidden
- 409: Conflict (z.B. Duplikat oder Referenz-Integrität)
- 500: Server Error

**Pagination**:
- Query: `?page=1&limit=50`
- Response: `{ data: [...], total: 1000, page: 1 }`

---

### 5. Authentication & Authorization Strategy

**Research Finding**: ✅ RESOLVED

**Decision**: Admin-basierte Authentifizierung

**Requirements** (aus Constitution Check):
- ✅ Admin-Authentifizierung erforderlich
- ✅ Benutzerrollen: Admin (Schreib-Zugriff), Viewer (Lese-Zugriff)
- ✅ Audit Logging für Änderungen
- ✅ Session-basiert oder JWT

**Recommended Approach**:
- JWT-Token mit `admin_id` und `role` Claims
- HttpOnly Cookies für Token-Storage
- 24-Stunden Token Expiry
- Refresh Token Mechanism

---

### 6. Frontend UI/UX Strategy

**Research Finding**: ✅ RESOLVED

**Decision**: Admin Dashboard mit separaten Modulen pro Entity

**Features**:
- ✅ Navigation (Sidebar mit Links zu Modalen/Pages)
- ✅ CRUD Forms mit Live-Validierung
- ✅ Größenkategorie Dropdown (XS/S/M/L/XL) statt Zahleneingabe
- ✅ Ökologische Zeigerwerte als Slider/Input (1-9)
- ✅ Hierarchische Habitat-Auswahl
- ✅ Multi-Select für Many-to-Many Relationships
- ✅ Success/Error Notifications
- ✅ Bestätigungsdialoge für Löschungen

---

## Remaining Clarifications

**ALL CLARIFICATIONS RESOLVED!** ✅

| # | Topic | Decision | Status |
|----|-------|----------|--------|
| 1 | Backend Language/Framework | PHP 8+ / Laravel 11 | ✅ RESOLVED |
| 2 | Frontend Framework | Vite + TailwindCSS + DaisyUI + Mary UI | ✅ RESOLVED |
| 3 | Database Engine | MySQL 8+ | ✅ RESOLVED |
| 4 | Authentication Method | Laravel Sanctum (SPA-optimized) | ✅ RESOLVED |
| 5 | ORM/Query Builder | Eloquent | ✅ RESOLVED |

**Status**: Phase 0 100% COMPLETE - Ready for Phase 1 Design & Contracts

---

## Assumptions Made (To Be Validated)

- ✅ Deployment: Single server or container-based (Docker)
- ✅ Auth existing: Assuming authentication system will be built alongside
- ✅ Database available: SQL database will be set up before backend development
- ✅ Browser compatibility: Modern browsers (Chrome, Firefox, Safari, Edge)

---

## Phase 0 Status

**Completion**: ✅ 100% COMPLETE

**Phase 0 Deliverables**:
1. ✅ User provided Technology Stack decisions
2. ✅ Verified technology choices align with constitutional requirements
3. ✅ Finalized research.md with all technology decisions
4. ✅ Ready to proceed to Phase 1 (Design & Contracts)

**Technology Stack Confirmed**:
- Backend: PHP 8.2+ / Laravel 12 + Eloquent + Sanctum
- Frontend: Vite + TailwindCSS + DaisyUI + Mary UI
- Database: MySQL 8+
- Testing: PHPUnit + Pest
- Development: Laravel Telescope

**Laravel Version Justification**:
Laravel 12 (Feb 2025) vs Laravel 11: Längere Support bis Feb 2027, minimale Breaking Changes, modernere PHP-Anforderungen

**Next Phase**: Phase 1 - Design & Contracts (data-model.md, contracts/, quickstart.md)

