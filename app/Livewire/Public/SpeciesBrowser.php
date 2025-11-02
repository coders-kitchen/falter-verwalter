<?php

namespace App\Livewire\Public;

use App\Models\Species;
use App\Models\Family;
use App\Models\Habitat;
use App\Models\Region;
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
        $query = Species::with('family', 'habitats', 'regions')
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

        // Endangered status filter - now based on pivot conservation_status
        if ($this->endangeredStatus === 'endangered') {
            $query->whereHas('regions', function ($q) {
                $q->wherePivot('conservation_status', 'gefährdet');
            });
        } elseif ($this->endangeredStatus === 'not_endangered') {
            $query->whereDoesntHave('regions', function ($q) {
                $q->wherePivot('conservation_status', 'gefährdet');
            });
        }

        // Region filter - filter by specific regions
        if (!empty($this->regionIds)) {
            $query->whereHas('regions', function ($q) {
                $q->whereIn('regions.id', $this->regionIds);
            });
        }

        return view('livewire.public.species-browser', [
            'species' => $query->paginate(50),
            'families' => Family::where('type', 'butterfly')->orderBy('name')->get(),
            'habitats' => Habitat::orderBy('name')->get(),
            'regions' => Region::orderBy('code')->get(),
        ]);
    }

    public function resetFilters()
    {
        $this->reset(['search', 'familyId', 'genusId', 'habitatIds', 'endangeredStatus', 'regionIds']);
        $this->resetPage();
    }
}
