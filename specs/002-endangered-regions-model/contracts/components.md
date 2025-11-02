# Component Contracts: Endangered Regions Model

**Feature**: 002-endangered-regions-model
**Date**: 2025-11-02

---

## Livewire Component Contracts

### 1. SpeciesManager (Updated)

**Location**: `app/Livewire/SpeciesManager.php`
**Template**: `resources/views/livewire/species-manager.blade.php`
**Purpose**: Admin interface for creating/editing species with new region assignment

#### Public Properties
```php
public $showModal = false;
public $species = null;
public $form = [
    'name' => '',
    'scientific_name' => '',
    'family_id' => '',
    // ... existing fields ...
    'selected_region_ids' => [],  // NEW: Array of region IDs
    'conservation_status' => [],  // NEW: Array of [region_id => status]
];
```

#### New/Updated Methods
```php
/**
 * Initialize form with species regions
 */
public function openEditModal(Species $species)
{
    // ... existing code ...
    $this->form['selected_region_ids'] =
        $species->regions()->pluck('regions.id')->toArray();
    $this->form['conservation_status'] =
        $species->regions()
            ->pluck('conservation_status', 'regions.id')
            ->toArray();
}

/**
 * Add a region to the species
 * @param int $regionId
 */
public function addRegion($regionId): void
{
    if (!in_array($regionId, $this->form['selected_region_ids'])) {
        $this->form['selected_region_ids'][] = $regionId;
        // Set default conservation status
        $this->form['conservation_status'][$regionId] = 'nicht_gefährdet';
    }
}

/**
 * Remove a region from the species
 * @param int $regionId
 */
public function removeRegion($regionId): void
{
    $this->form['selected_region_ids'] = array_filter(
        $this->form['selected_region_ids'],
        fn($id) => $id != $regionId
    );
    unset($this->form['conservation_status'][$regionId]);
}

/**
 * Update conservation status for a region
 * @param int $regionId
 * @param string $status
 */
public function updateConservationStatus($regionId, $status): void
{
    $this->form['conservation_status'][$regionId] = $status;
}

/**
 * Save species with new region assignments
 */
public function save(): void
{
    $this->validate();

    // Prepare pivot data
    $regionData = [];
    foreach ($this->form['selected_region_ids'] as $regionId) {
        $regionData[$regionId] = [
            'conservation_status' =>
                $this->form['conservation_status'][$regionId] ?? 'nicht_gefährdet'
        ];
    }

    // Save or create
    if ($this->species) {
        $this->species->update($this->formData());
        $this->species->regions()->sync($regionData);
    } else {
        $species = Species::create($this->formData());
        $species->regions()->attach($regionData);
    }

    // ... notification, close modal ...
}
```

#### Form Validation
```php
protected $rules = [
    'form.name' => 'required|string|max:255',
    'form.family_id' => 'required|exists:families,id',
    'form.selected_region_ids' => 'required|array|min:1',
    'form.selected_region_ids.*' => 'integer|exists:regions,id',
    'form.conservation_status.*' => 'in:nicht_gefährdet,gefährdet',
];
```

#### Blade Template Structure
```blade
<div wire:loading.class="opacity-50">
    <!-- Geographic Distribution Section -->
    <div class="card bg-base-200 mb-6">
        <div class="card-body">
            <h3 class="card-title">📍 Geografische Verbreitung</h3>
            <p class="text-sm opacity-75">Wähle Regionen, in denen die Art vorkommt</p>

            <!-- Region Selector (Checkboxes/Multi-Select) -->
            <div class="form-control">
                @foreach ($allRegions as $region)
                    <label class="label cursor-pointer">
                        <input type="checkbox"
                            wire:click="toggleRegion({{ $region->id }})"
                            @checked(in_array($region->id, $form['selected_region_ids']))
                            class="checkbox" />
                        <span class="label-text">{{ $region->code }} - {{ $region->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Conservation Status Section -->
    <div class="card bg-base-200">
        <div class="card-body">
            <h3 class="card-title">⚠️ Gefährdungsstatus</h3>
            <p class="text-sm opacity-75">Lege den Status für jede Region fest</p>

            <!-- Status Assignment (Dropdowns per Region) -->
            <div class="space-y-3">
                @foreach ($form['selected_region_ids'] as $regionId)
                    @php $region = $allRegions->find($regionId); @endphp
                    <div class="flex items-center gap-4">
                        <span class="font-semibold">{{ $region->code }}</span>
                        <select wire:change="updateConservationStatus({{ $regionId }}, $event.target.value)"
                                class="select select-bordered select-sm">
                            <option value="nicht_gefährdet"
                                @selected($form['conservation_status'][$regionId] === 'nicht_gefährdet')>
                                Nicht gefährdet
                            </option>
                            <option value="gefährdet"
                                @selected($form['conservation_status'][$regionId] === 'gefährdet')>
                                Gefährdet
                            </option>
                        </select>
                        <button wire:click="removeRegion({{ $regionId }})" class="btn btn-sm btn-ghost">
                            ✕
                        </button>
                    </div>
                @endforeach
            </div>

            @error('form.selected_region_ids')
                <p class="text-error text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
```

---

### 2. SpeciesBrowser (Updated)

**Location**: `app/Livewire/Public/SpeciesBrowser.php`
**Template**: `resources/views/livewire/public/species-browser.blade.php`
**Purpose**: Public species search and filter interface

#### Changes
- Update filter logic to use new `species.regions()` relationship
- Replace queries on `endangered_regions` with `regions`
- Update endangered status filter to check `conservation_status` pivot column

#### Filter Property (Update)
```php
public $regionIds = [];  // NEW: Region filter
```

#### Query Update
```php
public function render()
{
    $query = Species::with('family', 'habitats', 'regions')
        ->orderBy('name');

    // ... existing filters ...

    // NEW: Region filter
    if (!empty($this->regionIds)) {
        $query->whereHas('regions', function ($q) {
            $q->whereIn('regions.id', $this->regionIds);
        });
    }

    // UPDATED: Endangered status filter
    if ($this->endangeredStatus === 'endangered') {
        $query->whereHas('regions', function($q) {
            $q->wherePivot('conservation_status', 'gefährdet');
        });
    } elseif ($this->endangeredStatus === 'not_endangered') {
        // Species with regions but all not_gefährdet, OR species with no regions
        $query->whereDoesntHave('regions', function($q) {
            $q->wherePivot('conservation_status', 'gefährdet');
        });
    }

    return view('livewire.public.species-browser', [
        'species' => $query->paginate(50),
        'regions' => Region::orderBy('code')->get(),
    ]);
}
```

---

### 3. RegionalDistributionMap (Updated)

**Location**: `app/Livewire/Public/RegionalDistributionMap.php`
**Purpose**: Display regional species distribution and endangerment

#### Query Update
```php
public function aggregateRegionData()
{
    $regions = Region::all();
    $this->regionData = [];
    $this->maxCount = 0;

    foreach ($regions as $region) {
        // Count endangered species in this region
        $count = $region->species()
            ->wherePivot('conservation_status', 'gefährdet')
            ->when($this->species, function ($query) {
                $query->where('species_id', $this->species->id);
            })
            ->distinct()
            ->count();

        $this->regionData[$region->code] = [
            'name' => $region->name,
            'code' => $region->code,
            'count' => $count,
            'id' => $region->id,
        ];

        if ($count > $this->maxCount) {
            $this->maxCount = $count;
        }
    }
}
```

---

### 4. SpeciesDetail (Updated)

**Location**: `app/Livewire/Public/SpeciesDetail.php`
**Purpose**: Display detailed species information including regions

#### Component Update
```php
public function mount(Species $species)
{
    $this->species = $species->load([
        'family',
        'habitats',
        'regions',  // CHANGED: from endangeredRegions
    ]);
}
```

#### Template Update (species-detail.blade.php)
```blade
<!-- Distribution Tab -->
<div>
    <h4 class="text-lg font-bold mb-3">Geografische Verbreitung</h4>

    @if ($species->regions->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach ($species->regions as $region)
                <div class="card bg-base-200">
                    <div class="card-body py-3">
                        <p class="font-semibold">{{ $region->code }} - {{ $region->name }}</p>
                        <p class="text-sm opacity-75">
                            Status:
                            @if ($region->pivot->conservation_status === 'gefährdet')
                                <span class="badge badge-error">Gefährdet</span>
                            @else
                                <span class="badge badge-success">Nicht gefährdet</span>
                            @endif
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info">
            Keine Regionen eingetragen
        </div>
    @endif
</div>
```

---

## API Response Contracts

### Regions Endpoint (if needed)
```json
{
  "data": [
    {
      "id": 1,
      "code": "NRW",
      "name": "Nord Rhein Westfalen",
      "description": "Region in western Germany",
      "created_at": "2025-11-02T10:00:00Z"
    }
  ]
}
```

### Species with Regions
```json
{
  "id": 1,
  "name": "Schwalbenschwanz",
  "scientific_name": "Papilio machaon",
  "regions": [
    {
      "id": 1,
      "code": "NRW",
      "name": "Nord Rhein Westfalen",
      "pivot": {
        "conservation_status": "gefährdet",
        "created_at": "2025-11-02T10:00:00Z"
      }
    }
  ]
}
```

---

## Database Query Contracts

### Get All Species in a Region
```sql
SELECT species.*
FROM species
INNER JOIN species_region ON species.id = species_region.species_id
WHERE species_region.region_id = ?
```

### Get Endangered Species by Region
```sql
SELECT species.*, species_region.conservation_status
FROM species
INNER JOIN species_region ON species.id = species_region.species_id
WHERE species_region.region_id = ?
  AND species_region.conservation_status = 'gefährdet'
```

### Count Species per Region
```sql
SELECT
  regions.id,
  regions.code,
  regions.name,
  COUNT(DISTINCT species_region.species_id) as total_count,
  SUM(CASE WHEN species_region.conservation_status = 'gefährdet' THEN 1 ELSE 0 END) as endangered_count
FROM regions
LEFT JOIN species_region ON regions.id = species_region.region_id
GROUP BY regions.id
```

---

## Event/Notification Contracts

### Species Region Created
```php
event(new \App\Events\SpeciesRegionAssigned(
    species: $species,
    region: $region,
    conservationStatus: 'nicht_gefährdet'
));
```

### Conservation Status Updated
```php
event(new \App\Events\ConservationStatusUpdated(
    species: $species,
    region: $region,
    oldStatus: 'nicht_gefährdet',
    newStatus: 'gefährdet'
));
```

---

## Testing Contracts

### Unit Test Expectations
- Species can have multiple regions
- Region conservation_status defaults to 'nicht_gefährdet'
- Deleting region deletes species_region entry
- Pivot data includes conservation_status

### Feature Test Expectations
- Admin can add region to species
- Conservation status updates correctly
- Public features display new data correctly
- Queries execute in <500ms

---

## References

- **Implementation Plan**: [plan.md](../plan.md)
- **Data Model**: [data-model.md](../data-model.md)
- **Quick Start**: [quickstart.md](../quickstart.md)
