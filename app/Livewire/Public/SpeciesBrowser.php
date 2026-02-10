<?php

namespace App\Livewire\Public;

use App\Models\DistributionArea;
use App\Models\Species;
use App\Models\Family;
use App\Models\Habitat;
use App\Models\Region;
use App\Models\ThreatCategory;
use Livewire\Component;
use Livewire\WithPagination;

class SpeciesBrowser extends Component
{
    use WithPagination;

    public $search = '';
    public $familyId = null;
    public $genusId = null;
    public $habitatIds = [];
    public $threatCategoryId = null;
    public $distributionAreaIds = [];

    protected $queryString = ['search', 'familyId', 'genusId', 'endangeredStatus', 'page'];

    public function render()
    {
        $query = Species::with('family', 'habitats', 'regions')
            ->orderBy('name');

        // Search filter
        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
                  //->orWhere('code', 'like', "%{$this->search}%");
        }

        // Family filter
        if ($this->familyId) {
            $query->where('family_id', $this->familyId);
        }

        // Habitat filter
        if (!empty($this->habitatIds)) {
            $query->whereHas('habitats', function ($q) {
                $q->whereIn('habitats.id', $this->habitatIds);
            });
        }

        if ($this->threatCategoryId) {
            $query->whereHas('distributionAreas', function($q) {
                $q->where('species_distribution_areas.threat_category_id', $this->threatCategoryId);
            });
        }

        // Region filter - filter by specific regions
        if (!empty($this->distributionAreaIds)) {
            $query->whereHas('distributionAreas', function ($q) {
                $q->whereIn('species_distribution_areas.distribution_area_id', $this->distributionAreaIds);
            });
        }

        return view('livewire.public.species-browser', [
            'species' => $query->paginate(50),
            'families' => Family::where('type', 'butterfly')->orderBy('name')->get(),
            'habitats' => Habitat::orderBy('name')->get(),
            'distributionAreas' => DistributionArea::orderBy('name')->get(),
            'threatCategories' => ThreatCategory::orderBy('rank')->get()
        ]);
    }

    public function resetFilters()
    {
        $this->reset(['search', 'familyId', 'genusId', 'habitatIds', 'threatCategoryId', 'distributionAreaIds']);
        $this->resetPage();
    }
}
