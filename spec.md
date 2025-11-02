# Epic: Erfasste Daten sollen Besuchern der App angezeigt werden

## Vision
Enable anonymous visitors to discover butterfly species through intuitive search and navigation, with rich data visualizations showing life cycles and geographic distribution.

## Goals
- Provide simple navigation and filtering for butterfly data
- Support two primary use cases: species search and plant-based discovery
- Visualize temporal (calendar) and spatial (map) data effectively
- Build visitor-facing features separate from admin management

---

## Functional Requirements

### FR1: Public Landing Page
**Description**: Introductory page explaining the app purpose and guiding visitors to main features

**Acceptance Criteria**:
- [ ] Landing page accessible at `/` (public)
- [ ] Clear explanation of app purpose (butterfly/plant database)
- [ ] Quick-access buttons to: "Schmetterlinge durchsuchen" (Use Case 1) and "Nach Pflanzen filtern" (Use Case 2)
- [ ] Optional: Featured/notable species showcase
- [ ] Responsive design for mobile and desktop
- [ ] Consistent styling with DaisyUI theme

**Routes**:
- `GET /` → Show landing page (if not authenticated)
- `GET /` → Redirect to admin dashboard (if authenticated)

---

### FR2: Use Case 1 - Species Search and Detail View

#### UC1.1: Species Browse/Search
**Description**: Visitor can search for and browse all butterfly species

**Acceptance Criteria**:
- [ ] Public search page at `GET /species`
- [ ] Search by species name or code
- [ ] Filter options:
  - [ ] By Family (Familie) - dropdown
  - [ ] By Genus (Gattung) - dropdown
  - [ ] By Habitat - multi-select
  - [ ] By Endangered Status - checkbox toggle
  - [ ] By Region/Endangered Region - multi-select
- [ ] Results displayed as paginated list with:
  - [ ] Species name and code
  - [ ] Thumbnail/icon
  - [ ] Brief description (truncated)
  - [ ] Quick endangered status badge
- [ ] Click species to view detail page

#### UC1.2: Species Detail View
**Description**: Comprehensive view of single species with all available information

**Acceptance Criteria**:
- [ ] Route: `GET /species/{id}`
- [ ] Display complete species information:
  - [ ] Basic Info: Name, code, Latin name, Familie, Unterfamilie, Gattung, Tribus, description
  - [ ] Taxonomy: Full hierarchical classification
- [ ] **Life Cycle Calendar Visualization**:
  - [ ] 12-month calendar grid showing by generation
  - [ ] Flight months highlighted (when adults visible)
  - [ ] Pupation periods shown
  - [ ] Visual distinction (colors/icons) for different phases
  - [ ] Display multiple generations if applicable
- [ ] **Geographic Information**:
  - [ ] List of regions where species is found
  - [ ] List of regions where species is endangered (marked visually)
  - [ ] Endangerment rating (if available)
- [ ] **Plant Associations**:
  - [ ] Nectar plants (🌺 Nektarpflanzen) - list with links
  - [ ] Larval host plants (🥬 Futterpflanzen) - list with links
  - [ ] Group by generation if relevant
- [ ] **Habitats**: List of preferred habitats with links
- [ ] Responsive layout for mobile and desktop
- [ ] Navigation: Back to species list, related species links

---

### FR3: Use Case 2 - Plant-Based Butterfly Discovery

#### UC2.1: Plant Selection and Butterfly Matching
**Description**: Visitor selects plants from their garden/area to discover what butterflies they attract

**Acceptance Criteria**:
- [ ] Public search page at `GET /discover-butterflies`
- [ ] Multi-select plant picker interface:
  - [ ] List/dropdown of all plants organized hierarchically (Familie → Gattung → Species)
  - [ ] Search/filter plants by name
  - [ ] Selected plants shown as chips/tags
  - [ ] Add/remove plants easily
- [ ] **Matching Logic**:
  - [ ] Use ANY matching: Show butterflies that use ANY of the selected plants
  - [ ] Match against: nectar_plants OR larval_host_plants
- [ ] **Results Display**:
  - [ ] Paginated list of matching species
  - [ ] For each species show:
    - [ ] Species name and code
    - [ ] Which selected plant(s) it uses (and for what purpose - nectar/larval)
    - [ ] Dangerous regions it appears in
    - [ ] Brief description
  - [ ] Click species to view detail page
- [ ] "Clear selections" button to reset
- [ ] Mobile-friendly interface

#### UC2.2: Plant Detail View
**Description**: View detailed information about a plant species

**Acceptance Criteria**:
- [ ] Route: `GET /plants/{id}`
- [ ] Display plant information:
  - [ ] Name, code, description
  - [ ] Taxonomy (Familie, Gattung)
  - [ ] Bloom period/season (if available in data)
- [ ] **Associated Butterflies**:
  - [ ] Butterflies that use it as nectar plant
  - [ ] Butterflies that use it as larval host plant
  - [ ] List with links to species detail
- [ ] **Habitats**: Where this plant is found
- [ ] Responsive design

---

### FR4: Map Visualization - Regional Distribution

#### UC4.1: Regional Species Map
**Description**: Interactive map showing species distribution and endangerment status by region

**Acceptance Criteria**:
- [ ] Accessible from: Species detail page OR separate `/map` page
- [ ] Map displays 9 endangered regions (NRW, WB, BGL, NTRL, NRBU, WT, WBEL, EI, SSl)
- [ ] **Data Display Modes** (tabs or toggle):
  - [ ] Mode A: Count of endangered species per region (show number and color gradient)
  - [ ] Mode B: Count of all species per region (show number and color gradient)
  - [ ] Color gradient: More species = darker color (red/orange/yellow scale)
- [ ] **Interactivity**:
  - [ ] Click region to filter currently viewed species
  - [ ] Hover to show region name and species count
  - [ ] Tooltip with detailed breakdown
- [ ] **Context**:
  - [ ] Use SVG or simple HTML/CSS regions
  - [ ] Include region labels (text)
  - [ ] Legend showing color meaning
- [ ] Mobile: Responsive or fall back to list view
- [ ] Performance: Efficient data aggregation

**Implementation Note**: Regions are: {code: name, code: name, ...}

---

### FR5: Calendar Visualization - Life Cycle Timeline

#### UC5.1: Species Life Cycle Calendar
**Description**: Visual calendar showing temporal aspects of species' life cycle

**Acceptance Criteria**:
- [ ] Displayed on species detail page
- [ ] **Visual Format**:
  - [ ] 12-month calendar grid (horizontal or vertical layout)
  - [ ] Each month as cell/column
  - [ ] Multiple generations shown separately (if applicable)
- [ ] **Data Layers** (shown via color/icon/pattern):
  - [ ] Flight months: Adult butterflies visible (primary color)
  - [ ] Pupation periods: Chrysalis/pupae dormant (secondary color)
  - [ ] Plant bloom times: Nectar/host plants active (optional accent)
- [ ] **Legend**: Clear legend explaining colors and symbols
- [ ] **Responsive**: Stack vertically on mobile, horizontal on desktop
- [ ] **Interaction**: Hover to show detailed information for month/generation
- [ ] Generation labeling: "1. Generation", "2. Generation", etc.

**Example Data Structure**:
```
Generation 1: Flight months [4,5,6,7], Pupation in autumn [8,9,10]
Generation 2: Flight months [8,9,10,11], Pupation in spring [3,4]
```

---

### FR6: Data Visibility & Access Control

#### AC6.1: Anonymous Access
**Description**: Ensure public features don't expose sensitive admin data

**Acceptance Criteria**:
- [ ] No authentication required for public pages
- [ ] Display only public-relevant data:
  - [ ] Basic species info, not internal notes
  - [ ] Habitats, plants, taxonomy (public reference data)
  - [ ] Endangerment status (public awareness)
- [ ] Hide admin-only fields:
  - [ ] Internal descriptions
  - [ ] Admin notes
  - [ ] System IDs (if sensitive)
- [ ] No access to management pages without authentication
- [ ] Clean separation of routes: `/species` (public) vs `/admin/species` (authenticated)

---

## Non-Functional Requirements

### NF1: Performance
- [ ] Species list search results load in <500ms
- [ ] Plant search results load in <500ms
- [ ] Map rendering on detail page loads in <200ms
- [ ] Calendar visualization renders in <100ms
- [ ] Handle 10,000+ species efficiently

### NF2: Usability
- [ ] Mobile-responsive design (tested on common breakpoints)
- [ ] Navigation intuitive with breadcrumbs
- [ ] Clear CTAs (Call-to-Action buttons)
- [ ] Loading states visible to user
- [ ] Empty state messages when no results

### NF3: Browser Compatibility
- [ ] Works on modern browsers (Chrome, Firefox, Safari, Edge)
- [ ] Graceful degradation for older browsers
- [ ] Mobile browsers (iOS Safari, Chrome Android)

### NF4: Accessibility
- [ ] Semantic HTML structure
- [ ] Proper heading hierarchy
- [ ] Alt text for images
- [ ] Keyboard navigation support
- [ ] Color not sole indicator (patterns/icons too)

---

## Technical Architecture

### Routes Structure

**Public Routes** (no authentication):
```
GET  /                          → Landing page
GET  /species                   → Species browse/search
GET  /species/{id}              → Species detail with calendar & map
GET  /discover-butterflies      → Plant-based butterfly search
GET  /plants/{id}               → Plant detail view
GET  /map                       → Full-page regional map (optional)
```

**Admin Routes** (authenticated, existing):
```
/admin/*                        → All existing management pages
```

### Components & Views

**Public Layout**:
- `resources/views/layouts/public.blade.php` - Base layout for public pages (simpler than admin layout, no sidebar)
- Header with navigation, footer, no admin menu

**Public Pages/Components**:
1. `resources/views/welcome.blade.php` - Landing page (updated)
2. `resources/views/public/species-list.blade.php` - Species browse
3. `resources/views/public/species-detail.blade.php` - Species profile
4. `resources/views/public/discover-butterflies.blade.php` - Plant-based search
5. `resources/views/public/plant-detail.blade.php` - Plant profile
6. `resources/views/public/map.blade.php` - Regional map (optional)

**Livewire Components**:
1. `app/Livewire/Public/SpeciesBrowser.php` - Search + filter + list species
2. `app/Livewire/Public/SpeciesDetail.php` - Display species with calendar and map
3. `app/Livewire/Public/PlantButterflyFinder.php` - Plant multi-select + matching butterflies
4. `app/Livewire/Public/PlantDetail.php` - Display plant information
5. `app/Livewire/Public/RegionalMap.php` - Interactive map visualization

**Visualization Components**:
1. `app/Livewire/Public/LifeCycleCalendar.php` - Calendar for flight/pupation periods
2. `app/Livewire/Public/RegionalDistributionMap.php` - SVG/HTML map with region data

### Database Queries & Optimization
- Eager load relationships: species→families, habitats, plants, endangered_regions
- Use indexes on: species.code, species.name, plants.name, families.code
- Cache aggregated data (region species counts) if performance needed

### Libraries & Dependencies
- **Chart.js**: For map/region data visualization
- **Livewire**: For interactive components
- **Blade**: Templating
- **DaisyUI + Tailwind**: Styling

---

## Data Model Notes

### Available Models & Relationships
```
Species
  - has_many: Generations
  - belongs_to_many: Habitats
  - belongs_to_many: EndangeredRegions
  - belongs_to_many: Plants (through Generation)

Generation
  - belongs_to: Species
  - has_many: nectar_plants (JSON array of plant IDs)
  - has_many: larval_host_plants (JSON array of plant IDs)
  - flight_start_month, flight_end_month (stored as integers 1-12)
  - pupation_start_month, pupation_end_month

Plant
  - belongs_to_many: Species (through Generation)
  - belongs_to: Family
  - has_many: Habitats

Habitat
  - belongs_to_many: Species
  - belongs_to: Family

Family (polymorphic for both Species and Plants)
  - code, name, parent_id (hierarchical)
  - type: 'butterfly' | 'plant'

EndangeredRegion
  - code, name (unique)
  - belongs_to_many: Species
```

### Public View Data Filtering
- Display only non-null/published fields
- Respect privacy: no internal admin notes in public views
- Show endangered_regions as awareness indicator (not as admin classification)

---

## Success Criteria

✅ All acceptance criteria met for FRs 1-6
✅ Performance benchmarks achieved
✅ Mobile-responsive on common devices
✅ Visitors can discover species via search (UC1)
✅ Visitors can discover species via plants (UC2)
✅ Calendar visualization displays correctly
✅ Map visualization interactive and informative
✅ No errors in browser console
✅ Public pages accessible without authentication
✅ Clean separation from admin section

---

## Future Enhancements (Out of Scope)
- User accounts for visitors to save favorites
- Advanced filters (flight period range, size, color, etc.)
- Photo gallery per species
- Detailed endangered species recovery plans
- Statistics dashboard for biologists
- Integration with external data sources
- Multi-language support

---

## Timeline & Priority
**Phase 1 (MVP)**: Landing page + species search + detail view with calendar
**Phase 2**: Plant-based discovery + map visualization
**Phase 3**: Polish, performance optimization, additional features
