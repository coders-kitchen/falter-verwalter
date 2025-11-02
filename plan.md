# Implementation Plan: Public Visitor Features

## Overview
This plan outlines the technical approach to implement public-facing features for butterfly and plant discovery, separating visitor experience from admin management.

---

## Phase 1: Foundation & Landing Page

### 1.1 Route Structure & Authentication Middleware
**Objective**: Establish public route patterns separate from admin

**Tasks**:
1. Create public route group in `routes/web.php`
   - Public routes: `/species`, `/discover-butterflies`, `/plants/{id}`, `/map`
   - Public middleware: no auth required
   - Admin routes: existing `/admin/*` unchanged

2. Update existing `/` route
   - Check if authenticated → redirect to `/admin/dashboard`
   - If not authenticated → show landing page

3. Create public layout view
   - File: `resources/views/layouts/public.blade.php`
   - Simpler than admin layout (no sidebar)
   - Include: header with nav, footer, main content area
   - Navigation links: Species Search, Plant Discovery, Map

**Implementation Details**:
```php
// routes/web.php additions
Route::group(['middleware' => ['guest']], function () {
    Route::get('/', function () {
        return view('welcome'); // Updated landing page
    })->name('home');

    Route::get('/species', [SpeciesBrowserController::class, 'index'])->name('species.index');
    Route::get('/species/{id}', [SpeciesBrowserController::class, 'show'])->name('species.show');
    Route::get('/discover-butterflies', [PlantButterflyFinderController::class, 'index'])->name('discover.index');
    Route::get('/plants/{id}', [PlantDetailController::class, 'show'])->name('plants.show');
    Route::get('/map', [RegionalMapController::class, 'index'])->name('map.index');
});

// Authenticated users
Route::middleware(['auth'])->group(function () {
    Route::redirect('/', '/admin/dashboard');
    // ... existing admin routes
});
```

**Deliverables**:
- ✅ Updated `routes/web.php`
- ✅ New `resources/views/layouts/public.blade.php`
- ✅ Updated `resources/views/welcome.blade.php` (landing page)

---

### 1.2 Public Landing Page
**Objective**: Create welcoming entry point that guides visitors

**Tasks**:
1. Update `resources/views/welcome.blade.php`
   - Hero section with app description
   - Two prominent CTAs:
     - "🦋 Nach Schmetterlingen suchen" → `/species`
     - "🌱 Nach Pflanzen filtern" → `/discover-butterflies`
   - Optional featured species carousel
   - Footer with info

2. Styling
   - Use DaisyUI hero component
   - Responsive design (mobile-first)
   - Consistent theme with admin section

**Deliverables**:
- ✅ Updated `resources/views/welcome.blade.php`

---

## Phase 2: Species Browse & Detail View

### 2.1 Species Browser Component
**Objective**: Allow visitors to search and filter species

**Tasks**:
1. Create Livewire component: `app/Livewire/Public/SpeciesBrowser.php`
   - Properties: `$search`, `$family_id`, `$genus_id`, `$habitat_ids`, `$endangered_status`, `$region_ids`, `$page`
   - Methods:
     - `render()`: Build paginated species list with filters
     - `updatedSearch()`: Live search by name/code
     - `updatedFamilyId()`: Filter by family
     - `updatedHabitatIds()`: Multi-select habitats
     - `resetFilters()`: Clear all filters
   - Eager load: family, genus, habitats, endangered_regions

2. Create view: `resources/views/livewire/public/species-browser.blade.php`
   - Filter sidebar:
     - Search input with live binding
     - Family dropdown
     - Genus dropdown
     - Habitat multi-select
     - Endangered status checkbox
     - Region multi-select
     - "Reset filters" button
   - Results area:
     - Pagination info
     - Species list with columns: name, code, endangered status badge, brief desc
     - Click row to view detail
   - Responsive: Filters collapse on mobile (accordion)

3. Add queries to models for filtering efficiency
   - `Species::byFamily($id)`, `byGenus($id)`, `byHabitat($ids)`, etc.

**Deliverables**:
- ✅ `app/Livewire/Public/SpeciesBrowser.php`
- ✅ `resources/views/livewire/public/species-browser.blade.php`
- ✅ `resources/views/public/species-list.blade.php` (wrapper view)

---

### 2.2 Species Detail Page with Calendar
**Objective**: Display comprehensive species information with life cycle calendar

**Tasks**:
1. Create Livewire component: `app/Livewire/Public/SpeciesDetail.php`
   - Properties: `$species` (loaded from route model binding)
   - Methods: `mount($species)`, `render()`
   - Load relations: family, habitats, endangeredRegions, generations (with plants)

2. Create view: `resources/views/livewire/public/species-detail.blade.php`
   - **Header Section**:
     - Species name, code, Latin name
     - Brief description
     - Endangered regions badges
   - **Info Tabs** (or sections):
     - **Taxonomy**: Familie, Unterfamilie, Gattung, Tribus (hierarchical display)
     - **Habitats**: List of habitats with links
     - **Plant Associations**:
       - By generation
       - Separate: Nectar plants (with emoji 🌺) and Larval host plants (with emoji 🥬)
       - Each plant is a link
     - **Distribution**: Regions where found vs endangered
   - **Life Cycle Calendar** (call component)
   - **Regional Map** (call component for this species' distribution)

3. Create calendar component: `app/Livewire/Public/LifeCycleCalendar.php`
   - Input: Species with generations data
   - Output: Visual calendar grid
   - 12 months x N generations (rows)
   - Color coding:
     - Flight months: #22c55e (green) or primary color
     - Pupation: #f97316 (orange) or secondary color
     - Plant bloom (optional): accent color
   - Legend below chart
   - Responsive: May stack vertically on mobile

4. Create calendar view: `resources/views/livewire/public/life-cycle-calendar.blade.php`
   - HTML table or CSS grid
   - Month headers: Jan, Feb, Mar, ..., Dec
   - Generation rows with labels
   - Cells with color background and icons
   - Hover tooltips showing details

**Deliverables**:
- ✅ `app/Livewire/Public/SpeciesDetail.php`
- ✅ `resources/views/livewire/public/species-detail.blade.php`
- ✅ `resources/views/public/species-detail.blade.php` (wrapper)
- ✅ `app/Livewire/Public/LifeCycleCalendar.php`
- ✅ `resources/views/livewire/public/life-cycle-calendar.blade.php`

**Route**:
```php
Route::get('/species/{species}', [SpeciesDetailController::class, 'show'])->name('species.show');
// Or Livewire route if using full component
Route::get('/species/{id}', SpeciesDetail::class)->name('species.show');
```

---

## Phase 3: Plant-Based Butterfly Discovery

### 3.1 Plant Butterfly Finder Component
**Objective**: Let visitors select plants and discover compatible butterflies

**Tasks**:
1. Create Livewire component: `app/Livewire/Public/PlantButterflyFinder.php`
   - Properties:
     - `$selectedPlantIds = []` (multi-select)
     - `$matchingSpecies = []` (query results)
     - `$showResults = false`
   - Methods:
     - `render()`: Display plant selector and results
     - `updateSelectedPlantIds()`: Trigger butterfly matching
     - `findButterflies()`: Query species using ANY of selected plants
     - `clearSelection()`: Reset form
   - Query logic:
     ```php
     // Pseudo-code
     if ($selectedPlantIds) {
         $species = Species::whereHas('generations', function($q) {
             $q->whereJsonContains('nectar_plants', $plant_id)
               ->orWhereJsonContains('larval_host_plants', $plant_id);
         })->get();
     }
     ```

2. Create view: `resources/views/livewire/public/plant-butterfly-finder.blade.php`
   - **Plant Selection**:
     - Multi-select dropdown with search
     - Plants hierarchically organized (Familie → Gattung → Species)
     - Selected plants shown as removable chips/tags
   - **Results Area**:
     - Show matching species list
     - For each species:
       - Name, code
       - Which selected plant(s) it uses: "(uses Brennnessel as larval host)"
       - Badge showing endangered regions
       - Link to detail page
     - Empty state: "Select plants to discover butterflies"
   - **Actions**:
     - "Clear selection" button
     - "View map" link (optional)

**Deliverables**:
- ✅ `app/Livewire/Public/PlantButterflyFinder.php`
- ✅ `resources/views/livewire/public/plant-butterfly-finder.blade.php`
- ✅ `resources/views/public/discover-butterflies.blade.php` (wrapper)

**Route**:
```php
Route::get('/discover-butterflies', PlantButterflyFinder::class)->name('discover.index');
```

---

### 3.2 Plant Detail Page
**Objective**: Display plant information and associated butterflies

**Tasks**:
1. Create Livewire component: `app/Livewire/Public/PlantDetail.php`
   - Input: Plant via route model binding
   - Load: family, habitats, related species

2. Create view: `resources/views/livewire/public/plant-detail.blade.php`
   - **Header**: Plant name, code, description
   - **Info Sections**:
     - **Taxonomy**: Familie, Gattung
     - **Habitats**: Where plant is found
     - **Bloom Period** (if stored): When plant is active
   - **Associated Butterflies**:
     - Section 1: Uses as nectar plant (🌺)
     - Section 2: Uses as larval host (🥬)
     - Each as list with links to species detail

**Deliverables**:
- ✅ `app/Livewire/Public/PlantDetail.php`
- ✅ `resources/views/livewire/public/plant-detail.blade.php`
- ✅ `resources/views/public/plant-detail.blade.php` (wrapper)

**Route**:
```php
Route::get('/plants/{plant}', PlantDetail::class)->name('plants.show');
```

---

## Phase 4: Map Visualization

### 4.1 Regional Distribution Map Component
**Objective**: Interactive map showing species distribution by region

**Tasks**:
1. Create Livewire component: `app/Livewire/Public/RegionalDistributionMap.php`
   - Properties:
     - `$species` (optional, for detail page integration)
     - `$displayMode = 'endangered'` (toggle: endangered | all)
     - `$regionData = []` (aggregated counts)
     - `$selectedRegion = null` (for filtering)
   - Methods:
     - `render()`: Fetch region species counts
     - `toggleDisplayMode()`: Switch between endangered/all
     - `selectRegion($regionCode)`: Filter species by region

2. Data aggregation (in component or query):
   - Build region data array with species counts
   - Two modes:
     - `endangered`: Count of species with endangered status in region
     - `all`: Total species found in region
   - Include color gradients based on counts

3. Create view: `resources/views/livewire/public/regional-distribution-map.blade.php`
   - **Control Panel**:
     - Tabs or radio buttons: "Gefährdete Arten" vs "Alle Arten"
     - Color gradient legend
   - **Map Display**:
     - SVG or HTML/CSS regions
     - 9 regions: NRW, WB, BGL, NTRL, NRBU, WT, WBEL, EI, SSl
     - Each region:
       - Colored according to count (darker = more)
       - Clickable to filter species
       - Hover tooltip: Region name + count
   - **Integration**:
     - Can be embedded in species detail page
     - Can be full-page at `/map`

4. Install Chart.js if needed:
   ```bash
   npm install chart.js
   ```

**Deliverables**:
- ✅ `app/Livewire/Public/RegionalDistributionMap.php`
- ✅ `resources/views/livewire/public/regional-distribution-map.blade.php`
- ✅ `resources/views/public/map.blade.php` (full-page wrapper, optional)

**Route**:
```php
Route::get('/map', RegionalDistributionMap::class)->name('map.index');
```

---

## Phase 5: Integration & Polish

### 5.1 Navigation & Breadcrumbs
**Objective**: Help users navigate between public pages

**Tasks**:
1. Add breadcrumb component: `app/Livewire/Public/Breadcrumbs.php`
   - Display page hierarchy
   - Links to parent pages

2. Update all public views to include breadcrumbs
   - Landing → Species List → Species Detail
   - Landing → Discover → Species Result Detail
   - Landing → Plant → Species Detail

**Deliverables**:
- ✅ `app/Livewire/Public/Breadcrumbs.php`
- ✅ Updated all public views

### 5.2 Public Layout Navigation
**Objective**: Consistent header/footer across public pages

**Tasks**:
1. Update `resources/views/layouts/public.blade.php`
   - Header:
     - Logo/home link
     - Main nav: "Schmetterlinge" (Species), "Pflanzen entdecken" (Discover), "Karte" (Map)
     - Search bar (quick access)
   - Footer:
     - About info
     - Links to admin (if applicable)
     - Copyright

2. Add global styles for public section
   - Different theme from admin (lighter, more inviting)
   - Mobile nav burger menu

**Deliverables**:
- ✅ Updated `resources/views/layouts/public.blade.php`

### 5.3 Performance Optimization
**Objective**: Ensure fast load times for public features

**Tasks**:
1. Add database indexes:
   ```sql
   CREATE INDEX idx_species_code ON species(code);
   CREATE INDEX idx_species_name ON species(name);
   CREATE INDEX idx_plants_name ON plants(name);
   CREATE INDEX idx_families_code ON families(code);
   ```

2. Implement eager loading in all Livewire components
   - Species: `with('family', 'habitats', 'endangeredRegions', 'generations')`
   - Plants: `with('family', 'habitats')`

3. Consider query caching for region aggregation
   - Cache region species counts (expires hourly)
   - Rebuild cache when species/regions updated in admin

4. Lazy-load calendar and map on detail pages if needed

**Deliverables**:
- ✅ Database migrations with indexes
- ✅ Updated components with eager loading
- ✅ Optional cache config

### 5.4 Testing & Validation
**Objective**: Ensure all features work correctly

**Tasks**:
1. Test species search/filter functionality
2. Test species detail display (all info visible)
3. Test calendar visualization rendering
4. Test plant-based butterfly discovery
5. Test map interactivity (click regions)
6. Test responsive design (mobile, tablet, desktop)
7. Test browser compatibility (Chrome, Firefox, Safari)
8. Performance: Measure load times, optimize if needed

**Deliverables**:
- ✅ Test results documentation
- ✅ Bug fixes if needed

---

## Implementation Order

**Week 1: Foundation**
1. Create public routes structure
2. Create public layout template
3. Update landing page

**Week 2: Species Features**
1. Create species browser component + view
2. Create species detail component + calendar visualization
3. Test species pages

**Week 3: Plant Discovery**
1. Create plant finder component
2. Create plant detail component
3. Test plant-based discovery

**Week 4: Visualizations & Polish**
1. Create regional map component
2. Add breadcrumbs
3. Polish UI and navigation
4. Performance optimization
5. Testing and fixes

---

## Dependencies & Libraries

- **Livewire 3.6.4**: Already installed, use for reactive components
- **Chart.js** (optional): For future enhanced charts
  ```bash
  npm install chart.js
  ```
- **DaisyUI**: Already configured, use components
- **Tailwind CSS v4**: Already configured
- **Laravel Blade**: Templating engine

---

## Database Considerations

### Required Data for Public Views
- Species: code, name, description, family_id, taxonomy fields
- Generations: species_id, flight_start_month, flight_end_month, pupation_start_month, pupation_end_month, nectar_plants (JSON), larval_host_plants (JSON)
- Plants: code, name, family_id, description
- Families: code, name, type ('butterfly' or 'plant'), parent_id (for hierarchy)
- Habitats: code, name, description, family_id
- EndangeredRegions: code, name
- Pivot tables: species_habitat, species_endangered_region

### Assumptions
- All necessary models and relationships exist (from previous epic)
- Data is already seeded with realistic species information
- User is admin_user id 1 for testing

---

## Security Considerations

- Public pages don't require authentication
- Don't expose admin-only fields in public views
- Use Blade's escaping for user-generated content (if any)
- Consider rate limiting on search endpoints if traffic is high

---

## Future Enhancements (Out of Scope)

- Advanced filters (flight period range, size, rarity)
- Photo gallery per species
- User favorites/bookmarks
- Statistics dashboard
- Multi-language support
- PDF export of species info
- Print-friendly views
