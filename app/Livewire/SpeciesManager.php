<?php

namespace App\Livewire;

use App\Models\Species;
use App\Models\Genus;
use App\Models\Habitat;
use App\Models\SpeciesPlant;
use App\Models\Tag;
use Livewire\Component;
use Livewire\WithPagination;

class SpeciesManager extends Component
{
    use WithPagination;

    public $search = '';
    public $tagSearch = '';
    public $showModal = false;
    public $species = null;
    public $form = [
        'name' => '',
        'scientific_name' => '',
        'genus_id' => '',
        'size_category' => '',
        'hibernation_stage' => '',
        'adult_phagy_level' => '',
        'larval_phagy_level' => '',
        'special_features' => '',
        'habitat_ids' => [],
        'tag_ids' => [],
    ];

    protected $rules = [
        'form.name' => 'required|string|max:255',
        'form.scientific_name' => 'nullable|string|max:255',
        'form.genus_id' => 'required|exists:genera,id',
        'form.size_category' => 'required|in:XS,S,M,L,XL',
        'form.hibernation_stage' => 'nullable|in:egg,larva,pupa,adult',
        'form.adult_phagy_level' => 'nullable|in:unbekannt,monophag,oligophag,polyphag',
        'form.larval_phagy_level' => 'nullable|in:unbekannt,monophag,oligophag,polyphag',
        'form.special_features' => 'nullable|string|max:255',
        'form.habitat_ids' => 'nullable|array',
        'form.habitat_ids.*' => 'integer|exists:habitats,id',
        'form.tag_ids' => 'nullable|array',
        'form.tag_ids.*' => 'integer|exists:tags,id',
    ];

    protected function messages(): array
    {
        return [
            'form.name.required' => 'Bitte einen Namen eingeben.',
            'form.name.max' => 'Der Name darf maximal 255 Zeichen lang sein.',
            'form.genus_id.required' => 'Bitte eine Gattung auswählen.',
            'form.genus_id.exists' => 'Die ausgewählte Gattung ist ungültig.',
            'form.size_category.required' => 'Bitte eine Größenkategorie auswählen.',
            'form.size_category.in' => 'Die Größenkategorie ist ungültig.',
            'form.hibernation_stage.in' => 'Das Überwinterungsstadium ist ungültig.',
            'form.adult_phagy_level.in' => 'Die Phagie-Stufe (Adulte) ist ungültig.',
            'form.larval_phagy_level.in' => 'Die Phagie-Stufe (Raupe) ist ungültig.',
            'form.special_features.max' => 'Die besonderen Merkmale dürfen maximal 255 Zeichen lang sein.',
            'form.habitat_ids.*.exists' => 'Mindestens ein ausgewählter Lebensraum ist ungültig.',
            'form.tag_ids.*.exists' => 'Mindestens ein ausgewähltes Tag ist ungültig.',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.name' => 'Name',
            'form.scientific_name' => 'Wissenschaftlicher Name',
            'form.genus_id' => 'Gattung',
            'form.size_category' => 'Größenkategorie',
            'form.hibernation_stage' => 'Überwinterungsstadium',
            'form.adult_phagy_level' => 'Phagie-Stufe (Adulte)',
            'form.larval_phagy_level' => 'Phagie-Stufe (Raupe)',
            'form.special_features' => 'Besondere Merkmale',
            'form.habitat_ids' => 'Lebensräume',
            'form.tag_ids' => 'Tags',
        ];
    }

    public function render()
    {
        $query = Species::with(['family', 'genus', 'tags'])->orderBy('name');

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

        $availableTags = Tag::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'description']);

        $selectedTagIds = collect($this->form['tag_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $selectedTags = $availableTags
            ->whereIn('id', $selectedTagIds)
            ->values();

        $search = mb_strtolower(trim((string) $this->tagSearch));
        $suggestedTags = $availableTags
            ->reject(fn ($tag) => in_array((int) $tag->id, $selectedTagIds, true))
            ->filter(function ($tag) use ($search) {
                if ($search === '') {
                    return true;
                }

                return str_contains(mb_strtolower((string) $tag->name), $search);
            })
            ->take(10)
            ->values();

        return view('livewire.species-manager', [
            'items' => $query->paginate(50),
            'genera' => $genera,
            'habitats' => $habitats,
            'selectedTags' => $selectedTags,
            'suggestedTags' => $suggestedTags,
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
        $this->form = $species->only(
            'name',
            'scientific_name',
            'genus_id',
            'size_category',
            'hibernation_stage',
            'adult_phagy_level',
            'larval_phagy_level',
            'special_features'
        );

        $this->form['habitat_ids'] = $species->habitats()->pluck('habitats.id')->toArray();
        $this->form['tag_ids'] = $species->tags()->pluck('tags.id')->toArray();
        $this->tagSearch = '';

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->tagSearch = '';
        $this->resetForm();
    }

    public function save()
    {
        $this->validate();

        $habitatIds = $this->form['habitat_ids'];
        $tagIds = collect($this->form['tag_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();
        $activeTagIds = Tag::query()
            ->where('is_active', true)
            ->whereIn('id', $tagIds)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if (count($activeTagIds) !== count($tagIds)) {
            $this->addError('form.tag_ids', 'Mindestens ein ausgewähltes Tag ist nicht aktiv.');
            return;
        }
        $formData = $this->form;

        unset($formData['habitat_ids'], $formData['tag_ids']);
        $genus = Genus::with('subfamily.family')->findOrFail((int) $formData['genus_id']);
        $formData['family_id'] = $genus->subfamily->family->id;

        if ($this->species) {
            $this->species->update($formData);
            $this->species->habitats()->sync($habitatIds);
            $this->species->tags()->sync($activeTagIds);
            $this->dispatch('notify', message: 'Art aktualisiert');
        } else {
            $species = Species::create(array_merge($formData, ['user_id' => auth()->id()]));
            $species->habitats()->sync($habitatIds);
            $species->tags()->sync($activeTagIds);
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
            'adult_phagy_level' => SpeciesPlant::PHAGY_UNKNOWN,
            'larval_phagy_level' => SpeciesPlant::PHAGY_UNKNOWN,
            'special_features' => '',
            'habitat_ids' => [],
            'tag_ids' => [],
        ];
        $this->species = null;
        $this->tagSearch = '';
    }

    public function addTag(int $tagId): void
    {
        if (!Tag::query()->where('id', $tagId)->where('is_active', true)->exists()) {
            return;
        }

        $ids = collect($this->form['tag_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->push($tagId)
            ->unique()
            ->values()
            ->all();

        $this->form['tag_ids'] = $ids;
        $this->tagSearch = '';
    }

    public function removeTag(int $tagId): void
    {
        $this->form['tag_ids'] = collect($this->form['tag_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0 && $id !== $tagId)
            ->values()
            ->all();
    }
}
