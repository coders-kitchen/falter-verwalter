<?php

namespace App\Livewire\Public;

use App\Models\Species;
use App\Models\Family;
use App\Models\Habitat;
use Livewire\Component;
use Livewire\WithPagination;

class SpeciesBrowser extends Component
{
    use WithPagination;

    public $search = '';
    public $familyId = null;
    public $genusId = null;
    public $habitatIds = [];
    public $endangeredStatus = null;
    public $regionIds = [];

    protected $queryString = ['search', 'familyId', 'genusId', 'endangeredStatus', 'page'];

    public function render()
    {
        $query = Species::with('family', 'habitats', 'endangeredRegions')
            ->orderBy('name');

        // Search filter
        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%")
                  ->orWhere('code', 'like', "%{$this->search}%");
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

        // Endangered status filter
        if ($this->endangeredStatus === 'endangered') {
            $query->whereHas('endangeredRegions');
        } elseif ($this->endangeredStatus === 'not_endangered') {
            $query->whereDoesntHave('endangeredRegions');
        }

        // Region filter
        if (!empty($this->regionIds)) {
            $query->whereHas('endangeredRegions', function ($q) {
                $q->whereIn('endangered_regions.id', $this->regionIds);
            });
        }

        return view('livewire.public.species-browser', [
            'species' => $query->paginate(50),
            'families' => Family::where('type', 'butterfly')->orderBy('name')->get(),
            'habitats' => Habitat::orderBy('name')->get(),
            'endangeredRegions' => \App\Models\EndangeredRegion::orderBy('code')->get(),
        ]);
    }

    public function resetFilters()
    {
        $this->reset(['search', 'familyId', 'genusId', 'habitatIds', 'endangeredStatus', 'regionIds']);
        $this->resetPage();
    }
}
