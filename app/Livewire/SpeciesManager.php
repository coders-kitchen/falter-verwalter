<?php

namespace App\Livewire;

use App\Models\Species;
use App\Models\Family;
use App\Models\Habitat;
use App\Models\Region;
use App\Models\DistributionArea;
use Livewire\Component;
use Livewire\WithPagination;

class SpeciesManager extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $species = null;
    public $form = [
        'name' => '',
        'scientific_name' => '',
        'family_id' => '',
        'size_category' => '',
        'generations_per_year' => '',
        'hibernation_stage' => '',
        // New fields for regions refactoring
        'selected_region_ids' => [],
        'selected_distribution_area_ids' => [],
        'conservation_status' => [],
        'habitat_ids' => []
    ];

    protected $rules = [
        'form.name' => 'required|string|max:255',
        'form.scientific_name' => 'nullable|string|max:255',
        'form.family_id' => 'required|exists:families,id',
        'form.size_category' => 'required|in:XS,S,M,L,XL',
        'form.generations_per_year' => 'nullable|integer|min:1',
        'form.hibernation_stage' => 'nullable|in:egg,larva,pupa,adult',
        // Validation for new regions feature - optional for creation, can be added later
        'form.selected_distribution_area_ids' => 'nullable|array',
        'form.selected_distribution_area_ids.*' => 'integer|exists:distribution_areas,id',
        'form.selected_region_ids' => 'nullable|array',
        'form.selected_region_ids.*' => 'integer|exists:regions,id',
        'form.conservation_status.*' => 'in:nicht_gefährdet,gefährdet',
    ];

    public function render()
    {
        $query = Species::with('family')->orderBy('name');

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('scientific_name', 'like', '%' . $this->search . '%');
        }

        // Get habitats with hierarchy ordering (root nodes first, then children)
        $habitats = $this->getHierarchicalHabitats();

        return view('livewire.species-manager', [
            'items' => $query->paginate(50),
            'families' => Family::orderBy('name')->get(),
            'allRegions' => Region::orderBy('code')->get(),
            'allDistributionAreas' => DistributionArea::orderBy('name')->get(),
            'habitats' => $habitats,
        ]);
    }

    /**
     * Get habitats ordered hierarchically with level information
     */
    private function getHierarchicalHabitats()
    {
        $habitats = Habitat::with('children')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        $result = [];
        $this->flattenHabitats($habitats, $result, 0);
        return $result;
    }


    /**
     * Recursively flatten habitat hierarchy while preserving level
     */
    private function flattenHabitats($habitats, &$result, $level)
    {
        foreach ($habitats as $habitat) {
            $habitat->level = $level;
            $result[] = $habitat;
            if ($habitat->children->isNotEmpty()) {
                $this->flattenHabitats($habitat->children, $result, $level + 1);
            }
        }
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(Species $species)
    {
        $this->species = $species;
        $this->form = $species->only('name', 'scientific_name', 'family_id', 'size_category', 'generations_per_year', 'hibernation_stage');

        // Load new regions data
        $this->form['selected_distribution_area_ids'] = $species->distributionAreas()->pluck('distribution_areas.id')->toArray();
        $this->form['selected_region_ids'] = $species->regions()->pluck('regions.id')->toArray();
        $this->form['conservation_status'] = $species->regions()
            ->pluck('conservation_status', 'regions.id')
            ->toArray();

        $this->form['habitat_ids'] = $species->habitats()->pluck('habitats.id')->toArray();

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    /**
     * Add a region to the species form (T015)
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
     * Remove a region from the species form (T016)
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
     * Update conservation status for a region (T026)
     */
    public function updateConservationStatus($regionId, $status): void
    {
        $this->form['conservation_status'][$regionId] = $status;
    }

    public function save()
    {
        $this->validate();

        $habitatIds = $this->form['habitat_ids'];
        $distributionAreas = $this->form['selected_distribution_area_ids'];
        $formData = $this->form;

        unset($formData['endangered_region_ids']);
        unset($formData['habitat_ids']);
        unset($formData['selected_distribution_area_ids']);

        // Prepare region data with pivot data (T018, T028)
        $regionData = [];
        foreach ($this->form['selected_region_ids'] as $regionId) {
            $regionData[$regionId] = [
                'conservation_status' => $this->form['conservation_status'][$regionId] ?? 'nicht_gefährdet'
            ];
        }
        unset($formData['selected_region_ids']);
        unset($formData['conservation_status']);

        if ($this->species) {
            $this->species->update($formData);
            $this->species->regions()->sync($regionData);
            $this->species->habitats()->sync($habitatIds);
            $this->species->distributionAreas()->sync($distributionAreas);
            $this->dispatch('notify', message: 'Art aktualisiert');
        } else {
            $species = Species::create(array_merge($formData, ['user_id' => auth()->id()]));
            $species->regions()->sync($regionData);
            $species->habitats()->sync($habitatIds);
            $species->distributionAreas()->sync($distributionAreas);
            $this->dispatch('notify', message: 'Art erstellt');
        }
        
        $this->closeModal();
        $this->resetPage();
    }

    public function delete(Species $species)
    {
        $species->delete();
        $this->dispatch('notify', message: 'Art gelöscht');
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->form = [
            'name' => '',
            'scientific_name' => '',
            'family_id' => '',
            'size_category' => '',
            'generations_per_year' => '',
            'hibernation_stage' => '',
            'endangered_region_ids' => [],
            'selected_region_ids' => [],
            'selected_distribution_area_ids' => [],
            'conservation_status' => [],
        ];
        $this->species = null;
    }
}
