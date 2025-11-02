# Tasks: Public Visitor Features Implementation

## Phase 1: Foundation & Landing Page

### Task 1.1: Create Public Route Structure
**Depends on**: Nothing
**Effort**: 1 hour
**Status**: Pending

**Description**:
Set up public routes separate from admin routes with proper middleware configuration.

**Acceptance Criteria**:
- [ ] Route file updated with public route group
- [ ] Public routes: /species, /species/{id}, /discover-butterflies, /plants/{id}, /map
- [ ] Public routes use `guest` middleware (no auth required)
- [ ] Root route `/` redirects authenticated users to /admin/dashboard
- [ ] Root route `/` shows landing page for unauthenticated users
- [ ] Route list shows all public routes when running `php artisan route:list`

**Commands**:
```bash
# Test routes
php artisan route:list | grep -E "^GET.*species|^GET.*plants|^GET.*discover|^GET.*map"
```

---

### Task 1.2: Create Public Layout Template
**Depends on**: Task 1.1
**Effort**: 2 hours
**Status**: Pending

**Description**:
Build base layout for public pages with header, footer, and main content area.

**Acceptance Criteria**:
- [ ] File created: `resources/views/layouts/public.blade.php`
- [ ] Header with:
  - [ ] Logo/home link (🦋 Falter Verwalter)
  - [ ] Navigation links: Schmetterlinge, Pflanzen entdecken, Karte
  - [ ] Responsive design (burger menu on mobile)
- [ ] Footer with:
  - [ ] App description/copyright
  - [ ] Links (optional)
- [ ] Main content area with `@yield('content')`
- [ ] Livewire styles/scripts included
- [ ] DaisyUI styling applied
- [ ] Mobile-responsive (tested on 375px, 768px, 1024px)
- [ ] Breadcrumb area (optional, can be empty)

**Structure**:
```html
<!DOCTYPE html>
<html>
<head>
    <!-- Meta, Vite, Livewire styles -->
</head>
<body class="bg-base-100">
    <!-- Header/Nav -->
    <!-- Breadcrumbs -->
    <!-- Main content -->
    <!-- Footer -->
</body>
</html>
```

---

### Task 1.3: Update Landing Page
**Depends on**: Task 1.2
**Effort**: 2 hours
**Status**: Pending

**Description**:
Create an inviting landing page that guides visitors to main features.

**Acceptance Criteria**:
- [ ] File updated: `resources/views/welcome.blade.php`
- [ ] Extends public layout
- [ ] Hero section with:
  - [ ] App title: "🦋 Falter Verwalter"
  - [ ] Tagline: "Entdecken Sie Schmetterlinge und Pflanzen"
  - [ ] Brief description of app purpose
- [ ] Two prominent CTAs:
  - [ ] Button: "🦋 Nach Schmetterlingen suchen" → `/species`
  - [ ] Button: "🌱 Nach Pflanzen filtern" → `/discover-butterflies`
- [ ] Optional featured species section (show 3-4 interesting species)
- [ ] Footer with info
- [ ] Fully responsive design
- [ ] DaisyUI hero component used
- [ ] Tested in browser: renders without errors

---

## Phase 2: Species Browse & Detail View

### Task 2.1: Create Species Browser Livewire Component
**Depends on**: Task 1.1
**Effort**: 3 hours
**Status**: Pending

**Description**:
Build searchable, filterable species browser for public visitors.

**Acceptance Criteria**:
- [ ] File created: `app/Livewire/Public/SpeciesBrowser.php`
- [ ] Properties:
  - [ ] `$search = ''`
  - [ ] `$familyId = null`
  - [ ] `$genusId = null`
  - [ ] `$habitatIds = []`
  - [ ] `$endangeredStatus = null`
  - [ ] `$regionIds = []`
- [ ] Methods:
  - [ ] `render()`: Returns paginated species list (50 per page)
  - [ ] Query includes: with('family', 'genus', 'habitats', 'endangeredRegions')
  - [ ] Search filters by: name or code (case-insensitive)
  - [ ] Results sorted by: name ascending
- [ ] Validation: No security issues (input sanitized)
- [ ] Component renders without errors

**Query Logic**:
```php
// Pseudo-code for render()
$query = Species::with('family', 'habitats', 'endangeredRegions');

if ($this->search) {
    $query->where('name', 'like', "%{$this->search}%")
          ->orWhere('code', 'like', "%{$this->search}%");
}

if ($this->familyId) {
    $query->where('family_id', $this->familyId);
}

// ... other filters

return view('livewire.public.species-browser', [
    'species' => $query->paginate(50),
    'families' => Family::where('type', 'butterfly')->get(),
    // ... other data for filters
]);
```

---

### Task 2.2: Create Species Browser View
**Depends on**: Task 2.1
**Effort**: 2 hours
**Status**: Pending

**Description**:
Build the user interface for species browser with filters and results.

**Acceptance Criteria**:
- [ ] File created: `resources/views/livewire/public/species-browser.blade.php`
- [ ] Filter section (left sidebar or top, responsive):
  - [ ] Search input: `wire:model.live="search"` with placeholder "Nach Art oder Code suchen..."
  - [ ] Family dropdown: `wire:model="familyId"`
  - [ ] Genus dropdown: `wire:model="genusId"` (optional)
  - [ ] Habitat multi-select: `wire:model="habitatIds"`
  - [ ] Endangered status: checkbox toggle
  - [ ] Region multi-select: `wire:model="regionIds"`
  - [ ] "Filter zurücksetzen" button
- [ ] Results area:
  - [ ] Table with columns: Name, Code, Beschreibung, Gefährdet, Aktionen
  - [ ] Each row clickable to species detail
  - [ ] Endangered regions shown as badges
  - [ ] Description truncated to 50 chars
- [ ] Pagination:
  - [ ] Show page info: "Seite X von Y"
  - [ ] Previous/Next buttons
  - [ ] Links to pages
- [ ] Empty state: "Keine Arten gefunden" with reset button
- [ ] Mobile responsive:
  - [ ] Filters collapse to accordion on mobile
  - [ ] Table scrollable horizontally
- [ ] Tested in browser: all filters work, pagination works

---

### Task 2.3: Create Species List Wrapper View
**Depends on**: Task 2.2
**Effort**: 30 minutes
**Status**: Pending

**Description**:
Create wrapper view that loads the species browser component.

**Acceptance Criteria**:
- [ ] File created: `resources/views/public/species-list.blade.php`
- [ ] Extends public layout
- [ ] Title: "Schmetterlinge durchsuchen"
- [ ] Loads Livewire component: `@livewire('Public\\SpeciesBrowser')`
- [ ] Renders without errors

---

### Task 2.4: Create Species Detail Component
**Depends on**: Task 1.1
**Effort**: 3 hours
**Status**: Pending

**Description**:
Build the detailed species profile page with all information.

**Acceptance Criteria**:
- [ ] File created: `app/Livewire/Public/SpeciesDetail.php`
- [ ] Properties:
  - [ ] `$species` (loaded via route model binding)
- [ ] Methods:
  - [ ] `mount($species)`: Load species with all relations
  - [ ] `render()`: Return species with eager-loaded data
- [ ] Eager load:
  - [ ] `with('family', 'habitats', 'endangeredRegions', 'generations.plants')`
- [ ] Component accessible by route: `/species/{species}`
- [ ] No errors when rendering

---

### Task 2.5: Create Species Detail View
**Depends on**: Task 2.4
**Effort**: 3 hours
**Status**: Pending

**Description**:
Build the UI for species detail page with all information sections.

**Acceptance Criteria**:
- [ ] File created: `resources/views/livewire/public/species-detail.blade.php`
- [ ] Header section:
  - [ ] Species name (large heading)
  - [ ] Code and Latin name (if available)
  - [ ] Brief description
  - [ ] Endangered regions as badges (red badges for endangered)
- [ ] Info sections:
  - [ ] **Taxonomie**:
    - [ ] Familie, Unterfamilie, Gattung, Tribus
    - [ ] Each as hierarchical display
  - [ ] **Lebensräume**: List of habitats as links
  - [ ] **Pflanzliche Verbindungen**:
    - [ ] Grouped by generation if multiple
    - [ ] 🌺 Nektarpflanzen: List with links
    - [ ] 🥬 Futterpflanzen: List with links
    - [ ] For each generation separately
  - [ ] **Verbreitung**:
    - [ ] All regions where species found
    - [ ] Regions where endangered (marked differently)
- [ ] Call Life Cycle Calendar component (from Task 2.6)
- [ ] Call Regional Distribution Map component (from Task 4.1)
- [ ] Navigation:
  - [ ] Back to species list button
  - [ ] Breadcrumb: Home > Schmetterlinge > [Species Name]
- [ ] Mobile responsive
- [ ] Tested in browser: all sections visible and properly formatted

---

### Task 2.6: Create Life Cycle Calendar Component
**Depends on**: Task 2.4
**Effort**: 2 hours
**Status**: Pending

**Description**:
Build calendar visualization showing flight and pupation periods.

**Acceptance Criteria**:
- [ ] File created: `app/Livewire/Public/LifeCycleCalendar.php`
- [ ] Input: `$species` (with generations loaded)
- [ ] Methods:
  - [ ] `render()`: Prepare calendar data from generations
  - [ ] Process generations: extract flight/pupation months
- [ ] Logic:
  - [ ] Create 12-month x N-generation grid
  - [ ] For each month in generation:
    - [ ] Determine if flight month, pupation month, or neither
    - [ ] Store data for view rendering
- [ ] No database queries (use passed data)
- [ ] Component renders without errors

---

### Task 2.7: Create Life Cycle Calendar View
**Depends on**: Task 2.6
**Effort**: 2 hours
**Status**: Pending

**Description**:
Build the visual calendar grid for life cycle display.

**Acceptance Criteria**:
- [ ] File created: `resources/views/livewire/public/life-cycle-calendar.blade.php`
- [ ] Layout:
  - [ ] Title: "🔄 Lebenszykluskalender"
  - [ ] Legend below chart explaining colors:
    - [ ] Green (#22c55e): Flight months (Flugmonate)
    - [ ] Orange (#f97316): Pupation (Verpuppung)
    - [ ] (Optional) Accent: Plant bloom times
  - [ ] Calendar grid:
    - [ ] Rows: Generation labels (1. Generation, 2. Generation, etc.)
    - [ ] Columns: Month abbreviations (Jan, Feb, Mar, ..., Dez)
    - [ ] Cells: Color-coded by data
    - [ ] Hover tooltip: Show month name and phase
- [ ] HTML Structure:
  - [ ] Use CSS Grid or HTML table
  - [ ] Responsive: Stack vertically on mobile (< 768px)
  - [ ] Horizontal layout on desktop
- [ ] Styling:
  - [ ] Use Tailwind classes
  - [ ] DaisyUI colors
  - [ ] Clear visual hierarchy
- [ ] Tested:
  - [ ] Renders with multiple generations
  - [ ] Renders with single generation
  - [ ] Mobile responsive
  - [ ] Colors display correctly

---

### Task 2.8: Create Species Detail Wrapper View
**Depends on**: Task 2.5, 2.7
**Effort**: 30 minutes
**Status**: Pending

**Description**:
Create wrapper view that loads species detail component.

**Acceptance Criteria**:
- [ ] File created: `resources/views/public/species-detail.blade.php`
- [ ] Extends public layout
- [ ] Loads Livewire component: `@livewire('Public\\SpeciesDetail')`
- [ ] Route parameter: `{species}` passed to component
- [ ] Renders without errors

---

## Phase 3: Plant-Based Butterfly Discovery

### Task 3.1: Create Plant Butterfly Finder Component
**Depends on**: Task 1.1
**Effort**: 3 hours
**Status**: Pending

**Description**:
Build component for plant-based butterfly discovery with multi-select.

**Acceptance Criteria**:
- [ ] File created: `app/Livewire/Public/PlantButterflyFinder.php`
- [ ] Properties:
  - [ ] `$selectedPlantIds = []` (multi-select array)
  - [ ] `$matchingSpecies = []` (query results)
  - [ ] `$showResults = false`
- [ ] Methods:
  - [ ] `render()`: Return plants and matching species
  - [ ] `updatedSelectedPlantIds()`: Trigger species matching
  - [ ] `findButterflies()`: Query species using ANY of selected plants
  - [ ] `clearSelection()`: Reset form
- [ ] Query Logic:
  ```php
  // Find species using ANY of selected plants
  if ($this->selectedPlantIds) {
      $species = Species::whereHas('generations', function($q) {
          $q->whereJsonContains('nectar_plants', $this->selectedPlantIds)
            ->orWhereJsonContains('larval_host_plants', $this->selectedPlantIds);
      })->with('endangeredRegions')->get();
  }
  ```
- [ ] Load all plants: `Plant::with('family')->orderBy('name')->get()`
- [ ] Paginate results: 20 per page
- [ ] Component renders without errors

---

### Task 3.2: Create Plant Butterfly Finder View
**Depends on**: Task 3.1
**Effort**: 2 hours
**Status**: Pending

**Description**:
Build UI for plant-based butterfly discovery.

**Acceptance Criteria**:
- [ ] File created: `resources/views/livewire/public/plant-butterfly-finder.blade.php`
- [ ] Layout:
  - [ ] Title: "🌱 Welche Schmetterlinge lockt dein Garten an?"
  - [ ] Description: "Wähle deine Gartenpflanzen aus..."
- [ ] Plant Selection Section:
  - [ ] Multi-select with search:
    ```html
    <select wire:model="selectedPlantIds" multiple size="6">
        @foreach ($plants as $plant)
            <optgroup label="{{ $plant->family->name }}">
                <option value="{{ $plant->id }}">
                    {{ str_repeat('— ', $plant->family->level ?? 0) }}{{ $plant->name }}
                </option>
            </optgroup>
        @endforeach
    </select>
    ```
  - [ ] Show selected plants as removable chips/tags
  - [ ] "Clear selection" button
- [ ] Results Section:
  - [ ] Initially: "Wähle Pflanzen aus um Schmetterlinge zu entdecken"
  - [ ] When selected:
    - [ ] Table with results: Name, Code, Uses, Regions, Actions
    - [ ] For each species:
      - [ ] "Uses Brennnessel as larval host" (which plants matched)
      - [ ] Endangered regions as badges
      - [ ] "View details" link
    - [ ] Pagination
  - [ ] Empty state if no matches: "Keine Schmetterlinge für diese Pflanzen"
- [ ] Mobile responsive
- [ ] Tested: Multi-select works, plant search works, results display correctly

---

### Task 3.3: Create Plant Butterfly Finder Wrapper View
**Depends on**: Task 3.2
**Effort**: 30 minutes
**Status**: Pending

**Description**:
Create wrapper view for plant-based discovery.

**Acceptance Criteria**:
- [ ] File created: `resources/views/public/discover-butterflies.blade.php`
- [ ] Extends public layout
- [ ] Loads component: `@livewire('Public\\PlantButterflyFinder')`
- [ ] Renders without errors

---

### Task 3.4: Create Plant Detail Component
**Depends on**: Task 1.1
**Effort**: 2 hours
**Status**: Pending

**Description**:
Build component for individual plant detail page.

**Acceptance Criteria**:
- [ ] File created: `app/Livewire/Public/PlantDetail.php`
- [ ] Properties:
  - [ ] `$plant` (via route model binding)
- [ ] Methods:
  - [ ] `mount($plant)`: Load plant with relations
  - [ ] `render()`: Return plant with eager load
- [ ] Eager load:
  - [ ] `with('family', 'habitats', 'generations')`
  - [ ] Load species that use this plant
- [ ] Component accessible: `/plants/{plant}`
- [ ] No errors when rendering

---

### Task 3.5: Create Plant Detail View
**Depends on**: Task 3.4
**Effort**: 2 hours
**Status**: Pending

**Description**:
Build UI for plant detail page.

**Acceptance Criteria**:
- [ ] File created: `resources/views/livewire/public/plant-detail.blade.php`
- [ ] Header:
  - [ ] Plant name (large heading)
  - [ ] Code and Latin name
  - [ ] Description
- [ ] Info sections:
  - [ ] **Systematik**: Familie, Gattung
  - [ ] **Lebensräume**: List of habitats
  - [ ] **Blütezeit** (if data available): Bloom period
- [ ] Associated Butterflies:
  - [ ] 🌺 **Nektarpflanzen für** (butterflies using as nectar):
    - [ ] List with links to species
  - [ ] 🥬 **Futterpflanze für** (butterflies using as host):
    - [ ] List with links to species
- [ ] Navigation:
  - [ ] Back button
  - [ ] Breadcrumb
- [ ] Mobile responsive
- [ ] Tested: All sections visible

---

### Task 3.6: Create Plant Detail Wrapper View
**Depends on**: Task 3.5
**Effort**: 30 minutes
**Status**: Pending

**Description**:
Create wrapper view for plant detail.

**Acceptance Criteria**:
- [ ] File created: `resources/views/public/plant-detail.blade.php`
- [ ] Extends public layout
- [ ] Loads component: `@livewire('Public\\PlantDetail')`
- [ ] Renders without errors

---

## Phase 4: Map Visualization

### Task 4.1: Create Regional Distribution Map Component
**Depends on**: Task 1.1
**Effort**: 3 hours
**Status**: Pending

**Description**:
Build interactive map showing species by region.

**Acceptance Criteria**:
- [ ] File created: `app/Livewire/Public/RegionalDistributionMap.php`
- [ ] Properties:
  - [ ] `$species = null` (optional, for species detail integration)
  - [ ] `$displayMode = 'endangered'` (toggle: endangered | all)
  - [ ] `$regionData = []` (aggregated data)
  - [ ] `$selectedRegion = null`
- [ ] Methods:
  - [ ] `render()`: Aggregate region data
  - [ ] `toggleDisplayMode($mode)`: Switch display mode
  - [ ] `selectRegion($code)`: Filter by region
- [ ] Data Aggregation:
  - [ ] If `$species` provided: Show only this species' regions
  - [ ] Else: Aggregate all species
  - [ ] For mode 'endangered': Count endangered_region associations
  - [ ] For mode 'all': Count all species in region
  - [ ] Generate color gradient (darkest = most species)
- [ ] Component renders without errors

---

### Task 4.2: Create Regional Distribution Map View
**Depends on**: Task 4.1
**Effort**: 3 hours
**Status**: Pending

**Description**:
Build interactive SVG/HTML map of endangered regions.

**Acceptance Criteria**:
- [ ] File created: `resources/views/livewire/public/regional-distribution-map.blade.php`
- [ ] Layout:
  - [ ] Title: "📍 Regionale Verbreitung"
  - [ ] Control panel:
    - [ ] Tabs or radio buttons:
      - [ ] "Gefährdete Arten" (endangered)
      - [ ] "Alle Arten" (all)
    - [ ] Color gradient legend (light = few, dark = many)
- [ ] Map Display:
  - [ ] SVG or HTML/CSS representation of 9 regions:
    - [ ] NRW (Nordrhein-Westfalen)
    - [ ] WB (Weser-Bergland)
    - [ ] BGL (Bergisches Land)
    - [ ] NTRL (Niederrhein-Tiefland)
    - [ ] NRBU (Niederrheinisches Buchtland)
    - [ ] WT (Westerwald)
    - [ ] WBEL (Westerberg)
    - [ ] EI (Eiffel)
    - [ ] SSl (South Saarland)
  - [ ] Each region:
    - [ ] Colored by species count (gradient)
    - [ ] Clickable to filter species (optional)
    - [ ] Hover tooltip: Region code, name, count
    - [ ] Display: "NRW: 23 gefährdete Arten"
- [ ] Responsive:
  - [ ] Desktop: Full map display
  - [ ] Mobile: Stack to list or simplified layout
- [ ] Performance:
  - [ ] Lazy-load if on detail page
  - [ ] Cache region data if needed

**Note**: Can use simple HTML/CSS colored divs or SVG. Chart.js optional.

---

### Task 4.3: Create Full Map Page Wrapper View
**Depends on**: Task 4.2
**Effort**: 30 minutes
**Status**: Pending

**Description**:
Create full-page map view (optional).

**Acceptance Criteria**:
- [ ] File created: `resources/views/public/map.blade.php`
- [ ] Extends public layout
- [ ] Loads component: `@livewire('Public\\RegionalDistributionMap')`
- [ ] Route: `GET /map`
- [ ] Renders without errors

---

## Phase 5: Integration & Polish

### Task 5.1: Add Breadcrumb Navigation Component
**Depends on**: Task 2.5, 3.5
**Effort**: 1 hour
**Status**: Pending

**Description**:
Build reusable breadcrumb component.

**Acceptance Criteria**:
- [ ] File created: `app/Livewire/Public/Breadcrumbs.php`
- [ ] Input: `$breadcrumbs` (array of name => route pairs)
- [ ] Output: Linked breadcrumb trail
- [ ] Example:
  ```
  Home > Schmetterlinge > Tagpfauenauge
  ```
- [ ] Links functional
- [ ] Renders without errors

---

### Task 5.2: Update Public Layout Header & Navigation
**Depends on**: Task 1.2
**Effort**: 2 hours
**Status**: Pending

**Description**:
Enhance public layout with proper header and navigation.

**Acceptance Criteria**:
- [ ] Updated: `resources/views/layouts/public.blade.php`
- [ ] Header improvements:
  - [ ] Logo/home link: 🦋 Falter Verwalter
  - [ ] Navigation menu:
    - [ ] "Schmetterlinge" → `/species`
    - [ ] "Pflanzen entdecken" → `/discover-butterflies`
    - [ ] "Karte" → `/map`
  - [ ] Search bar (quick access to species search)
  - [ ] Mobile burger menu (responsive)
- [ ] Footer:
  - [ ] Copyright info
  - [ ] Links (optional)
  - [ ] Brief about text
- [ ] Styling:
  - [ ] DaisyUI navbar and footer components
  - [ ] Inviting, lighter theme than admin
  - [ ] Consistent with admin branding
- [ ] Tested:
  - [ ] Navigation links work
  - [ ] Mobile menu functional
  - [ ] Responsive on all sizes

---

### Task 5.3: Add Database Indexes for Performance
**Depends on**: Nothing (can do anytime)
**Effort**: 1 hour
**Status**: Pending

**Description**:
Add database indexes to optimize query performance.

**Acceptance Criteria**:
- [ ] Migration file created: `database/migrations/YYYY_MM_DD_XXXXXX_add_public_indexes.php`
- [ ] Indexes added:
  - [ ] `idx_species_code` on `species(code)`
  - [ ] `idx_species_name` on `species(name)`
  - [ ] `idx_plants_name` on `plants(name)`
  - [ ] `idx_families_code` on `families(code)`
  - [ ] `idx_endangered_regions_code` on `endangered_regions(code)`
- [ ] Migration runs successfully: `php artisan migrate`
- [ ] Verification: `SHOW INDEXES FROM species;` shows new indexes

---

### Task 5.4: Test Species Browse & Search Functionality
**Depends on**: Task 2.2, 2.3
**Effort**: 2 hours
**Status**: Pending

**Description**:
Comprehensive testing of species search and filtering.

**Acceptance Criteria**:
- [ ] Test in browser at `/species`:
  - [ ] Page loads without errors
  - [ ] Species list displays (20+ species)
  - [ ] Search by name works (type "tagpfauenauge" → results filter)
  - [ ] Search by code works (type code → results filter)
  - [ ] Family filter works (select family → results filter)
  - [ ] Habitat multi-select works (select habitats → results filter)
  - [ ] Endangered toggle works
  - [ ] Region multi-select works
  - [ ] Pagination works (navigate pages)
  - [ ] Reset filters button clears all
  - [ ] Click species → navigates to detail page
- [ ] No console errors
- [ ] Mobile responsive (test on 375px width)
- [ ] Results load in <500ms

---

### Task 5.5: Test Species Detail Page
**Depends on**: Task 2.5, 2.7
**Effort**: 2 hours
**Status**: Pending

**Description**:
Test species detail view with all information.

**Acceptance Criteria**:
- [ ] Test in browser at `/species/1` (or any species):
  - [ ] Page loads without errors
  - [ ] Title and code display
  - [ ] Description visible
  - [ ] Taxonomy section shows Familie, Gattung, etc.
  - [ ] Habitats list visible
  - [ ] Plant associations display:
    - [ ] 🌺 Nectar plants listed
    - [ ] 🥬 Larval host plants listed
    - [ ] Links to plant detail pages work
  - [ ] Endangered regions shown as badges
  - [ ] Life Cycle Calendar displays:
    - [ ] Months visible (Jan-Dez)
    - [ ] Generations shown (if multiple)
    - [ ] Colors correct (flight vs pupation)
    - [ ] Legend visible and clear
  - [ ] Regional map displays (if integrated)
  - [ ] Breadcrumb shows: Home > Schmetterlinge > [Species Name]
  - [ ] Back button works
- [ ] Mobile responsive
- [ ] No console errors

---

### Task 5.6: Test Plant Discovery & Plant Detail
**Depends on**: Task 3.2, 3.5
**Effort**: 2 hours
**Status**: Pending

**Description**:
Test plant-based butterfly discovery and plant detail pages.

**Acceptance Criteria**:
- [ ] Test `/discover-butterflies`:
  - [ ] Page loads
  - [ ] Plant selector displays all plants
  - [ ] Multi-select works (select multiple plants)
  - [ ] Selected plants show as chips/tags
  - [ ] Results load when plants selected:
    - [ ] Shows species that use ANY of selected plants
    - [ ] Clear indication which plant it uses
  - [ ] Clear selection button works
  - [ ] Pagination works
  - [ ] Click species → detail page
- [ ] Test `/plants/1` (or any plant):
  - [ ] Page loads
  - [ ] Plant name and info display
  - [ ] Taxonomic info shows
  - [ ] Associated butterflies section:
    - [ ] 🌺 Nectar butterflies listed with links
    - [ ] 🥬 Host butterflies listed with links
  - [ ] Mobile responsive
- [ ] No console errors

---

### Task 5.7: Test Map Visualization
**Depends on**: Task 4.2
**Effort**: 1.5 hours
**Status**: Pending

**Description**:
Test map display and interactivity.

**Acceptance Criteria**:
- [ ] Test `/map` (or map on species detail):
  - [ ] Map displays all 9 regions
  - [ ] Each region colored by species count
  - [ ] Color gradient makes sense (darker = more)
  - [ ] Toggle between "Endangered" and "All" modes works
  - [ ] Tooltip on hover shows region info
  - [ ] (Optional) Click region to filter species
  - [ ] Legend visible and accurate
- [ ] Mobile responsive (regions readable on mobile)
- [ ] No console errors
- [ ] Data aggregation correct (counts match database)

---

### Task 5.8: Test Responsive Design
**Depends on**: All view tasks (2.2-2.7, 3.2, 3.5, 4.2)
**Effort**: 1.5 hours
**Status**: Pending

**Description**:
Comprehensive responsive design testing.

**Acceptance Criteria**:
- [ ] Test on breakpoints:
  - [ ] 375px (mobile phone)
  - [ ] 768px (tablet)
  - [ ] 1024px (laptop)
  - [ ] 1440px (desktop)
- [ ] All pages tested:
  - [ ] Landing page
  - [ ] Species list
  - [ ] Species detail
  - [ ] Plant discovery
  - [ ] Plant detail
  - [ ] Map (if full page)
- [ ] Issues checked:
  - [ ] No horizontal scrolling (except if needed)
  - [ ] Text readable
  - [ ] Buttons tappable (min 44px)
  - [ ] Images scale properly
  - [ ] Navigation accessible
- [ ] Tools: Chrome DevTools, mobile device (if available)

---

### Task 5.9: Performance Testing & Optimization
**Depends on**: All feature tasks
**Effort**: 2 hours
**Status**: Pending

**Description**:
Optimize performance for public pages.

**Acceptance Criteria**:
- [ ] Load time measurements:
  - [ ] Species list: < 500ms
  - [ ] Species detail: < 500ms
  - [ ] Plant discovery: < 500ms
  - [ ] Map rendering: < 200ms
- [ ] Optimization checks:
  - [ ] All components use eager loading (no N+1 queries)
  - [ ] Database indexes in place
  - [ ] Livewire polling disabled (use events only)
  - [ ] Images lazy-loaded if applicable
- [ ] Tools: Laravel Debugbar, browser DevTools
- [ ] If performance issues found: Implement fixes
  - [ ] Add caching for region aggregation
  - [ ] Optimize queries
  - [ ] Defer heavy computations

---

### Task 5.10: Browser Compatibility Testing
**Depends on**: All view tasks
**Effort**: 1 hour
**Status**: Pending

**Description**:
Test on multiple browsers for compatibility.

**Acceptance Criteria**:
- [ ] Browsers tested:
  - [ ] Chrome (latest)
  - [ ] Firefox (latest)
  - [ ] Safari (latest, if available)
  - [ ] Edge (latest)
- [ ] For each browser:
  - [ ] Pages load without errors
  - [ ] Layout renders correctly
  - [ ] Interactive features work
  - [ ] No console errors
- [ ] Mobile browsers:
  - [ ] Chrome Android
  - [ ] Safari iOS (if available)
- [ ] Issues noted and fixed if critical

---

### Task 5.11: Bug Fixes & Final Polish
**Depends on**: Tasks 5.4-5.10
**Effort**: 2 hours
**Status**: Pending

**Description**:
Fix any bugs found in testing and polish UI/UX.

**Acceptance Criteria**:
- [ ] All bugs from testing tasks fixed
- [ ] UI enhancements:
  - [ ] Loading states visible
  - [ ] Error messages clear
  - [ ] Empty states helpful
  - [ ] Transitions/animations smooth
- [ ] Copy review:
  - [ ] All German text correct and consistent
  - [ ] Button labels clear
  - [ ] Help text helpful
- [ ] Final visual review:
  - [ ] Colors consistent
  - [ ] Spacing/padding consistent
  - [ ] Font sizes readable
  - [ ] Hierarchy clear
- [ ] No console warnings or errors

---

## Summary

**Total Tasks**: 53
**Phases**:
- Phase 1 (Foundation): 3 tasks
- Phase 2 (Species): 8 tasks
- Phase 3 (Plants): 6 tasks
- Phase 4 (Map): 3 tasks
- Phase 5 (Testing/Polish): 11 tasks

**Estimated Total Effort**: 45-50 hours

**Recommended Team/Timeline**:
- Solo developer: 10-12 weeks (part-time)
- Team of 2: 5-6 weeks
- Sprint-based: 2-3 sprints of 2 weeks each

---

## Completion Checklist

- [ ] All 53 tasks completed
- [ ] All acceptance criteria met
- [ ] All testing tasks passed
- [ ] No critical bugs
- [ ] Performance benchmarks met
- [ ] Mobile responsive verified
- [ ] Browser compatibility confirmed
- [ ] Ready for user acceptance testing
