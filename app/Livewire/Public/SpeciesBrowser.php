<?php

namespace App\Livewire\Public;

use App\Models\DistributionArea;
use App\Models\Genus;
use App\Models\Species;
use App\Models\Family;
use App\Models\Habitat;
use App\Models\Subfamily;
use App\Models\ThreatCategory;
use App\Models\Tribe;
use Livewire\Component;
use Livewire\WithPagination;

class SpeciesBrowser extends Component
{
    use WithPagination;

    public $search = '';
    public $familyId = null;
    public $subfamilyId = null;
    public $tribeId = null;
    public $genusId = null;
    public $habitatIds = [];
    public $threatCategoryId = null;
    public $distributionAreaIds = [];

    protected $queryString = ['search', 'familyId', 'subfamilyId', 'tribeId', 'genusId', 'threatCategoryId', 'distributionAreaIds', 'page'];

    public function updatedFamilyId(): void
    {
        $this->subfamilyId = null;
        $this->tribeId = null;
        $this->genusId = null;
        $this->resetPage();
    }

    public function updatedSubfamilyId(): void
    {
        $this->tribeId = null;
        $this->genusId = null;
        $this->resetPage();
    }

    public function updatedTribeId(): void
    {
        $this->genusId = null;
        $this->resetPage();
    }

    public function render()
    {
        $query = Species::with('family', 'genus.subfamily.family', 'genus.tribe', 'habitats')
            ->orderBy('name');

        // Search filter
        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
                  //->orWhere('code', 'like', "%{$this->search}%");
        }

        // Family filter
        if ($this->familyId) {
            $familyId = (int) $this->familyId;
            $query->where(function ($q) use ($familyId) {
                $q->where('family_id', $familyId)
                    ->orWhereHas('genus.subfamily.family', function ($sq) use ($familyId) {
                        $sq->where('families.id', $familyId);
                    });
            });
        }

        // Subfamily filter
        if ($this->subfamilyId) {
            $subfamilyId = (int) $this->subfamilyId;
            $query->whereHas('genus.subfamily', function ($q) use ($subfamilyId) {
                $q->where('subfamilies.id', $subfamilyId);
            });
        }

        // Tribe filter
        if ($this->tribeId) {
            $tribeId = (int) $this->tribeId;
            $query->whereHas('genus.tribe', function ($q) use ($tribeId) {
                $q->where('tribes.id', $tribeId);
            });
        }

        // Genus filter
        if ($this->genusId) {
            $query->where('genus_id', (int) $this->genusId);
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

        $families = Family::where('type', 'butterfly')
            ->orderBy('name')
            ->get();

        $subfamilies = Subfamily::query()
            ->whereHas('family', function ($q) {
                $q->where('type', 'butterfly');
                if ($this->familyId) {
                    $q->where('id', (int) $this->familyId);
                }
            })
            ->orderBy('name')
            ->get();

        $tribes = Tribe::query()
            ->whereHas('subfamily.family', function ($q) {
                $q->where('type', 'butterfly');
                if ($this->familyId) {
                    $q->where('id', (int) $this->familyId);
                }
            })
            ->when($this->subfamilyId, function ($q) {
                $q->where('subfamily_id', (int) $this->subfamilyId);
            })
            ->orderBy('name')
            ->get();

        $genera = Genus::with(['subfamily.family', 'tribe'])
            ->whereHas('subfamily.family', function ($q) {
                $q->where('type', 'butterfly');
                if ($this->familyId) {
                    $q->where('id', (int) $this->familyId);
                }
            })
            ->when($this->subfamilyId, function ($q) {
                $q->where('subfamily_id', (int) $this->subfamilyId);
            })
            ->when($this->tribeId, function ($q) {
                $q->where('tribe_id', (int) $this->tribeId);
            })
            ->orderBy('name')
            ->get();

        return view('livewire.public.species-browser', [
            'species' => $query->paginate(50),
            'families' => $families,
            'subfamilies' => $subfamilies,
            'tribes' => $tribes,
            'genera' => $genera,
            'habitats' => Habitat::orderBy('name')->get(),
            'distributionAreas' => DistributionArea::query()
                ->select(['id', 'name', 'code'])
                ->orderBy('name')
                ->get(),
            'threatCategories' => ThreatCategory::orderBy('rank')->get()
        ]);
    }

    public function resetFilters()
    {
        $this->reset(['search', 'familyId', 'subfamilyId', 'tribeId', 'genusId', 'habitatIds', 'threatCategoryId', 'distributionAreaIds']);
        $this->resetPage();
    }
}
