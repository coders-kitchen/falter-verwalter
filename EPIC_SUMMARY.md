# Epic Summary: Public Visitor Features

## Status: Planning Complete ✅

The specification, implementation plan, and detailed task list for the next epic "Erfasste Daten sollen Besuchern der App angezeigt werden" have been created.

---

## What Has Been Created

### 1. **spec.md** - Formal Feature Specification
Complete requirements document including:
- **Vision & Goals**: Enable anonymous visitors to discover butterflies
- **6 Functional Requirements**:
  - FR1: Public landing page
  - FR2: Species search and detail view
  - FR3: Plant-based butterfly discovery
  - FR4: Regional distribution map
  - FR5: Life cycle calendar visualization
  - FR6: Data visibility & access control
- **Non-Functional Requirements**: Performance, usability, accessibility, browser compatibility
- **Technical Architecture**: Route structure, component list, data models
- **Success Criteria**: Clear acceptance criteria for all features

### 2. **plan.md** - Implementation Plan
Technical architecture and step-by-step approach:
- **5 Phases** with detailed tasks
- **Route structure** for public pages (separate from admin)
- **Component list** with descriptions of what each component does
- **Implementation order** showing dependencies
- **Database considerations** and security notes
- **Dependencies & libraries**: Livewire, DaisyUI, Tailwind, Chart.js

### 3. **tasks.md** - Detailed Task List
**53 actionable tasks** organized by phase:
- **Phase 1**: 3 foundation tasks (routes, layout, landing page)
- **Phase 2**: 8 species-related tasks (browser, detail, calendar)
- **Phase 3**: 6 plant discovery tasks
- **Phase 4**: 3 map visualization tasks
- **Phase 5**: 11 testing and polish tasks

Each task includes:
- Acceptance criteria (checkbox list)
- Code examples where helpful
- Dependencies and effort estimate
- Implementation notes

---

## Key Features Overview

### For Visitors: Use Case 1 - Species Search
```
Landing Page
    ↓
Species List (searchable, filterable)
    ↓
Species Detail (with calendar & map)
```

**Filters available**:
- Search by name or code
- Family, Genus
- Habitat (multi-select)
- Endangered status
- Region/Endangered region (multi-select)

**Species Detail shows**:
- Complete taxonomy
- Life cycle calendar (flight months, pupation periods)
- Associated plants (nectar & larval host)
- Geographic distribution with endangerment status
- Regional map visualization

### For Visitors: Use Case 2 - Plant-Based Discovery
```
Landing Page
    ↓
Plant Discovery (multi-select plants)
    ↓
Matching Butterflies (uses ANY selected plant)
    ↓
Species Detail
```

**Process**:
1. Visitor selects plants from their garden
2. System finds all butterflies using ANY of those plants
3. Shows which butterflies are attracted
4. Links to full species details

### Visualizations

**1. Life Cycle Calendar**
- 12-month x N-generation grid
- Color-coded: Flight months (green), Pupation (orange)
- Shows temporal aspects of species lifecycle
- Responsive layout

**2. Regional Distribution Map**
- Interactive map of 9 endangered regions
- Two modes: Endangered species count vs All species count
- Color gradient (darker = more species)
- Click to filter species
- Hover for region details

---

## Technical Architecture

### Public Routes
```
GET  /                          Landing page
GET  /species                   Species browser (searchable, filterable)
GET  /species/{id}              Species detail with calendar & map
GET  /discover-butterflies      Plant-based butterfly discovery
GET  /plants/{id}               Plant detail page
GET  /map                       Full-page regional map (optional)
```

### Livewire Components
```
Public/SpeciesBrowser           Search & filter species
Public/SpeciesDetail            Single species with relations
Public/LifeCycleCalendar        Calendar visualization
Public/PlantButterflyFinder     Multi-select plants, find butterflies
Public/PlantDetail              Single plant with associated butterflies
Public/RegionalDistributionMap  Interactive regional map
Public/Breadcrumbs              Navigation breadcrumbs
```

### Blade Views
```
layouts/public.blade.php        Base layout (no sidebar, inviting theme)
welcome.blade.php               Landing page (hero + CTAs)
public/species-list.blade.php   Species browser wrapper
public/species-detail.blade.php Species detail wrapper
public/discover-butterflies.blade.php Plant discovery wrapper
public/plant-detail.blade.php   Plant detail wrapper
public/map.blade.php            Full-page map (optional)
```

### Database Optimization
- Eager loading in all components (no N+1 queries)
- Database indexes on: species code/name, plant name, family code
- Optional caching for region aggregation

---

## User Answers from Clarification Questions

### Species Detail Display
- ✅ Basic Info + Taxonomy
- ✅ Life Cycle Calendar
- ✅ All information (comprehensive profile)

### Plant-Based Search Matching
- ✅ Multi-select with ANY matching (flexible)
  - User picks multiple plants
  - See butterflies using ANY of them
  - Not restricted to species using ALL plants

### Map Visualization Data
- ✅ Endangered count per region
- ✅ All species distribution per region
- ✅ Color gradient (intensity based on count)

### Calendar Visualization Details
- ✅ Flight months
- ✅ Pupation periods
- ✅ Plant bloom times

### Additional Features
- ✅ Landing page with intro + quick access
- ✅ Charting: Chart.js (lightweight, good for regional data)
- ✅ Map interactivity: Click region to filter species

---

## Estimated Effort

| Phase | Tasks | Hours | Notes |
|-------|-------|-------|-------|
| 1 Foundation | 3 | 5-6 | Routes, layout, landing page |
| 2 Species | 8 | 15-18 | Browser, detail, calendar |
| 3 Plants | 6 | 10-12 | Discovery, detail, matching |
| 4 Map | 3 | 6-8 | Regional visualization |
| 5 Testing | 11 | 10-12 | Functional & acceptance testing |
| **TOTAL** | **53** | **45-50** | **2-3 weeks full-time or 2-3 months part-time** |

---

## Next Steps

### Option 1: Start Implementation Now
If ready to begin development, recommend starting with **Phase 1** (3 tasks):
1. ✏️ **Task 1.1**: Create public route structure
2. ✏️ **Task 1.2**: Create public layout template
3. ✏️ **Task 1.3**: Update landing page

These tasks establish the foundation and can be done in 1-2 hours.

### Option 2: Review & Adjust Specification
If you'd like to review the plans first:
- Read `spec.md` for feature requirements
- Read `plan.md` for technical approach
- Suggest any changes or additions
- I'll update documents accordingly

### Option 3: Skip Ahead
If you want to start with a specific phase (e.g., species detail), we can begin there. However, Phase 1 foundation tasks are prerequisite for other phases.

---

## Document Files

All planning documents are in the project root:

```
/home/peter/Development/falter-verwalter-v2-fresh/
├── spec.md          (70+ lines - Feature specification)
├── plan.md          (300+ lines - Implementation plan)
├── tasks.md         (400+ lines - Detailed task list)
└── EPIC_SUMMARY.md  (This file)
```

**How to use**:
1. **spec.md**: Reference for what to build (requirements)
2. **plan.md**: Reference for how to build it (architecture)
3. **tasks.md**: Work breakdown structure (do each task in order)

---

## Quality Assurance

Each task in `tasks.md` includes:
- ✅ Clear acceptance criteria
- ✅ Code examples where helpful
- ✅ Testing instructions
- ✅ Dependencies listed
- ✅ Effort estimates

Phase 5 includes 11 testing tasks:
- Functional testing (all features work)
- Responsive design testing (mobile, tablet, desktop)
- Performance testing (load times < 500ms)
- Browser compatibility testing
- Bug fixes and final polish

---

## Key Decision Points Made

Based on your input, the specification includes:

1. **Anonymous visitor access**: No authentication required
2. **Plant matching logic**: ANY matching (flexible, not strict)
3. **Map interactivity**: Click to filter species
4. **Calendar display**: Shows flight, pupation, and plant bloom times
5. **Charting library**: Chart.js (lightweight)
6. **Landing page**: Yes, with hero + quick access CTAs
7. **Route separation**: Public routes (/) separate from admin (/admin)

---

## Risk Mitigation

**Potential risks addressed**:

1. **Performance**: Eager loading, database indexes, optional caching
2. **Security**: Public views don't expose admin data, clean route separation
3. **Mobile usability**: Responsive design testing task included
4. **Data accuracy**: Query logic validated in planning
5. **Browser compatibility**: Testing task covers major browsers

---

## Communication & Documentation

**For stakeholders/users**:
- Once implementation starts, you can share `spec.md` to show what's being built
- Feature complete when all Phase 2-4 tasks done
- Testing phase (Phase 5) validates quality

**For developers**:
- `plan.md` provides technical architecture
- `tasks.md` gives step-by-step work items
- Each task has acceptance criteria for "done"

---

## Ready to Begin?

The foundation is laid. You can:

1. **Ask questions** about the spec or plan
2. **Request changes** to requirements or approach
3. **Start Phase 1** implementation (public routes + landing page)
4. **Review documents** before committing to timeline

What would you like to do next?
