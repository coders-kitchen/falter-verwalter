<?php

namespace App\Livewire;

use App\Models\Species;
use App\Models\Genus;
use App\Models\Habitat;
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
        'genus_id' => '',
        'size_category' => '',
        'hibernation_stage' => '',
        'sage_feeding_indicator' => 'keine genaue Angabe',
        'habitat_ids' => []
    ];

    protected $rules = [
        'form.name' => 'required|string|max:255',
        'form.scientific_name' => 'nullable|string|max:255',
        'form.genus_id' => 'required|exists:genera,id',
        'form.size_category' => 'required|in:XS,S,M,L,XL',
        'form.hibernation_stage' => 'nullable|in:egg,larva,pupa,adult',
        'form.sage_feeding_indicator' => 'required|in:Ja,Nein,keine genaue Angabe',
        'form.habitat_ids' => 'nullable|array',
        'form.habitat_ids.*' => 'integer|exists:habitats,id',
    ];

    public function render()
    {
        $query = Species::with(['family', 'genus'])->orderBy('name');

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('scientific_name', 'like', '%' . $this->search . '%');
        }

        // Get habitats with hierarchy ordering (root nodes first, then children)
        $habitats = $this->getHierarchicalHabitats();

        $genera = Genus::with(['subfamily.family', 'tribe'])
            ->whereHas('subfamily.family', function ($q) {
                $q->where('type', 'butterfly');
            })
            ->orderBy('name')
            ->get()
            ->map(function ($genus) {
                return [
                    'id' => $genus->id,
                    'name' => $genus->name,
                    'label' => $genus->displayLabel(),
                ];
            });

        return view('livewire.species-manager', [
            'items' => $query->paginate(50),
            'genera' => $genera,
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
        $this->form = $species->only('name', 'scientific_name', 'genus_id', 'size_category', 'hibernation_stage', 'sage_feeding_indicator');

        $this->form['habitat_ids'] = $species->habitats()->pluck('habitats.id')->toArray();

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function save()
    {
        $this->validate();

        $habitatIds = $this->form['habitat_ids'];
        $formData = $this->form;

        unset($formData['habitat_ids']);
        $genus = Genus::with('subfamily.family')->findOrFail((int) $formData['genus_id']);
        $formData['family_id'] = $genus->subfamily->family->id;

        if ($this->species) {
            $this->species->update($formData);
            $this->species->habitats()->sync($habitatIds);
            $this->dispatch('notify', message: 'Art aktualisiert');
        } else {
            $species = Species::create(array_merge($formData, ['user_id' => auth()->id()]));
            $species->habitats()->sync($habitatIds);
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
            'genus_id' => '',
            'size_category' => '',
            'hibernation_stage' => '',
            'sage_feeding_indicator' => 'keine genaue Angabe',
            'habitat_ids' => [],
        ];
        $this->species = null;
    }
}
