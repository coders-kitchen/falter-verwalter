<?php

namespace App\Livewire;

use App\Models\Plant;
use App\Models\LifeForm;
use App\Models\Habitat;
use App\Models\Genus;
use App\Models\ThreatCategory;
use Livewire\Component;
use Livewire\WithPagination;

class PlantManager extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $plant = null;

    public $form = [
        'name' => '',
        'scientific_name' => '',
        'family_id' => null,
        'genus_id' => null,
        'life_form_id' => null,
        'threat_category_id' => null,
        'light_number' => 5,
        'light_number_state' => 'numeric',
        'salt_number' => 5,
        'salt_number_state' => 'numeric',
        'temperature_number' => 5,
        'temperature_number_state' => 'numeric',
        'continentality_number' => 5,
        'continentality_number_state' => 'numeric',
        'reaction_number' => 5,
        'reaction_number_state' => 'numeric',
        'moisture_number' => 5,
        'moisture_number_state' => 'numeric',
        'moisture_variation' => 5,
        'moisture_variation_state' => 'numeric',
        'nitrogen_number' => 5,
        'nitrogen_number_state' => 'numeric',
        'bloom_start_month' => null,
        'bloom_end_month' => null,
        'bloom_color' => '',
        'plant_height_cm_from' => null,
        'plant_height_cm_until' => null,
        'lifespan' => null,
        'location' => '',
        'is_native' => false,
        'is_invasive' => false,
        'threat_status' => '',
        'heavy_metal_resistance' => 'nicht schwermetallresistent',
        'habitat_ids' => [],
    ];

    protected $rules = [
        'form.name' => 'required|string|max:255',
        'form.scientific_name' => 'nullable|string|max:255',
        'form.genus_id' => 'required|exists:genera,id',
        'form.life_form_id' => 'required|exists:life_forms,id',
        'form.threat_category_id' => 'nullable|exists:threat_categories,id',
        'form.light_number_state' => 'required|in:numeric,x,unknown',
        'form.salt_number_state' => 'required|in:numeric,x,unknown',
        'form.temperature_number_state' => 'required|in:numeric,x,unknown',
        'form.continentality_number_state' => 'required|in:numeric,x,unknown',
        'form.reaction_number_state' => 'required|in:numeric,x,unknown',
        'form.moisture_number_state' => 'required|in:numeric,x,unknown',
        'form.moisture_variation_state' => 'required|in:numeric,x,unknown',
        'form.nitrogen_number_state' => 'required|in:numeric,x,unknown',
        'form.light_number' => 'nullable|integer|between:1,9|required_if:form.light_number_state,numeric',
        'form.salt_number' => 'nullable|integer|between:0,9|required_if:form.salt_number_state,numeric',
        'form.temperature_number' => 'nullable|integer|between:1,9|required_if:form.temperature_number_state,numeric',
        'form.continentality_number' => 'nullable|integer|between:1,9|required_if:form.continentality_number_state,numeric',
        'form.reaction_number' => 'nullable|integer|between:1,9|required_if:form.reaction_number_state,numeric',
        'form.moisture_number' => 'nullable|integer|between:1,12|required_if:form.moisture_number_state,numeric',
        'form.moisture_variation' => 'nullable|integer|between:1,9|required_if:form.moisture_variation_state,numeric',
        'form.nitrogen_number' => 'nullable|integer|between:1,9|required_if:form.nitrogen_number_state,numeric',
        'form.bloom_start_month' => 'required|integer|between:1,12',
        'form.bloom_end_month' => 'required|integer|between:1,12',
        'form.bloom_color' => 'nullable|string|max:255',
        'form.plant_height_cm_from' => 'required|integer|min:0',
        'form.plant_height_cm_until' => 'required|integer|min:0',
        'form.lifespan' => 'required|in:annual,biennial,perennial',
        'form.location' => 'nullable|string|max:255',
        'form.is_native' => 'boolean',
        'form.is_invasive' => 'boolean',
        'form.threat_status' => 'nullable|string|max:255',
        'form.heavy_metal_resistance' => 'required|in:nicht schwermetallresistent,mäßig schwermetallresistent,ausgesprochen schwermetallresistent',
    ];

    protected function messages(): array
    {
        return [
            'form.name.required' => 'Bitte einen Namen eingeben.',
            'form.name.max' => 'Der Name darf maximal 255 Zeichen lang sein.',
            'form.genus_id.required' => 'Bitte eine Gattung auswählen.',
            'form.genus_id.exists' => 'Die ausgewählte Gattung ist ungültig.',
            'form.life_form_id.required' => 'Bitte eine Lebensart auswählen.',
            'form.life_form_id.exists' => 'Die ausgewählte Lebensart ist ungültig.',
            'form.bloom_start_month.required' => 'Bitte einen Startmonat für die Blüte auswählen.',
            'form.bloom_end_month.required' => 'Bitte einen Endmonat für die Blüte auswählen.',
            'form.lifespan.required' => 'Bitte eine Lebensdauer auswählen.',
            'form.plant_height_cm_from.required' => 'Bitte eine minimale Pflanzenhöhe angeben.',
            'form.plant_height_cm_from.integer' => 'Die minimale Pflanzenhöhe muss eine ganze Zahl sein.',
            'form.plant_height_cm_from.min' => 'Die minimale Pflanzenhöhe darf nicht negativ sein.',
            'form.plant_height_cm_until.required' => 'Bitte eine maximale Pflanzenhöhe angeben.',
            'form.plant_height_cm_until.integer' => 'Die maximale Pflanzenhöhe muss eine ganze Zahl sein.',
            'form.plant_height_cm_until.min' => 'Die maximale Pflanzenhöhe darf nicht negativ sein.',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.bloom_start_month' => 'Blühmonat Start',
            'form.bloom_end_month' => 'Blühmonat Ende',
            'form.lifespan' => 'Lebensdauer',
            'form.name' => 'Name',
            'form.genus_id' => 'Gattung',
            'form.life_form_id' => 'Lebensart',
            'form.plant_height_cm_from' => 'Pflanzenhöhe von',
            'form.plant_height_cm_until' => 'Pflanzenhöhe bis',
        ];
    }

    public function render()
    {
        $query = Plant::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('scientific_name', 'like', '%' . $this->search . '%');
        }

        $items = $query->with('lifeForm', 'family', 'threatCategory')
                       ->orderBy('name')
                       ->paginate(50);

        $genera = Genus::with(['subfamily.family', 'tribe'])
            ->whereHas('subfamily.family', function ($q) {
                $q->where('type', 'plant');
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

        $lifeForms = LifeForm::orderBy('name')->get();
        $threatCategories = ThreatCategory::orderBy('rank')->get();

        // Get habitats with hierarchy ordering (root nodes first, then children)
        $habitats = $this->getHierarchicalHabitats();

        return view('livewire.plant-manager', [
            'items' => $items,
            'genera' => $genera,
            'lifeForms' => $lifeForms,
            'threatCategories' => $threatCategories,
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

    public function openEditModal(Plant $plant)
    {
        $this->plant = $plant;
        $stateOrFallback = function (string $field) use ($plant): string {
            $stateField = "{$field}_state";
            return $plant->{$stateField} ?? ($plant->{$field} === null ? 'unknown' : 'numeric');
        };

        $this->form = [
            'name' => $plant->name,
            'scientific_name' => $plant->scientific_name,
            'family_id' => $plant->family_id,
            'genus_id' => $plant->genus_id,
            'life_form_id' => $plant->life_form_id,
            'threat_category_id' => $plant->threat_category_id,
            'light_number' => $plant->light_number,
            'light_number_state' => $stateOrFallback('light_number'),
            'salt_number' => $plant->salt_number,
            'salt_number_state' => $stateOrFallback('salt_number'),
            'temperature_number' => $plant->temperature_number,
            'temperature_number_state' => $stateOrFallback('temperature_number'),
            'continentality_number' => $plant->continentality_number,
            'continentality_number_state' => $stateOrFallback('continentality_number'),
            'reaction_number' => $plant->reaction_number,
            'reaction_number_state' => $stateOrFallback('reaction_number'),
            'moisture_number' => $plant->moisture_number,
            'moisture_number_state' => $stateOrFallback('moisture_number'),
            'moisture_variation' => $plant->moisture_variation,
            'moisture_variation_state' => $stateOrFallback('moisture_variation'),
            'nitrogen_number' => $plant->nitrogen_number,
            'nitrogen_number_state' => $stateOrFallback('nitrogen_number'),
            'bloom_start_month' => $plant->bloom_start_month,
            'bloom_end_month' => $plant->bloom_end_month,
            'bloom_color' => $plant->bloom_color,
            'plant_height_cm_from' => $plant->plant_height_cm_from,
            'plant_height_cm_until' => $plant->plant_height_cm_until,
            'lifespan' => $plant->lifespan,
            'location' => $plant->location,
            'is_native' => (bool) $plant->is_native,
            'is_invasive' => (bool) $plant->is_invasive,
            'threat_status' => $plant->threat_status,
            'heavy_metal_resistance' => $plant->heavy_metal_resistance ?? 'nicht schwermetallresistent',
            'habitat_ids' => $plant->habitats()->pluck('habitats.id')->toArray(),
        ];

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $formData = $this->form;
        $this->normalizeIndicatorValues($formData);
        $genus = Genus::with('subfamily.family')->findOrFail((int) $formData['genus_id']);
        $formData['family_id'] = $genus->subfamily->family->id;

        if ($this->plant) {
            $habitatIds = $this->form['habitat_ids'];
            unset($formData['habitat_ids']);

            $this->plant->update($formData);
            $this->plant->habitats()->sync($habitatIds);
        } else {
            $habitatIds = $this->form['habitat_ids'];
            unset($formData['habitat_ids']);

            $plant = Plant::create(array_merge($formData, ['user_id' => auth()->id()]));
            $plant->habitats()->sync($habitatIds);
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function delete(Plant $plant)
    {
        $plant->delete();
        $this->resetPage();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->plant = null;
        $this->form = [
            'name' => '',
            'scientific_name' => '',
            'family_id' => null,
            'genus_id' => null,
            'life_form_id' => null,
            'threat_category_id' => null,
            'light_number' => 5,
            'light_number_state' => 'numeric',
            'salt_number' => 5,
            'salt_number_state' => 'numeric',
            'temperature_number' => 5,
            'temperature_number_state' => 'numeric',
            'continentality_number' => 5,
            'continentality_number_state' => 'numeric',
            'reaction_number' => 5,
            'reaction_number_state' => 'numeric',
            'moisture_number' => 5,
            'moisture_number_state' => 'numeric',
            'moisture_variation' => 5,
            'moisture_variation_state' => 'numeric',
            'nitrogen_number' => 5,
            'nitrogen_number_state' => 'numeric',
            'bloom_start_month' => null,
            'bloom_end_month' => null,
            'bloom_color' => '',
            'plant_height_cm_from' => null,
            'plant_height_cm_until' => null,
            'lifespan' => null,
            'location' => '',
            'is_native' => false,
            'is_invasive' => false,
            'threat_status' => '',
            'heavy_metal_resistance' => 'nicht schwermetallresistent',
            'habitat_ids' => [],
        ];
        $this->resetErrorBag();
    }

    private function normalizeIndicatorValues(array &$formData): void
    {
        $fields = [
            'light_number',
            'salt_number',
            'temperature_number',
            'continentality_number',
            'reaction_number',
            'moisture_number',
            'moisture_variation',
            'nitrogen_number',
        ];

        foreach ($fields as $field) {
            $state = $formData["{$field}_state"] ?? 'numeric';
            if ($state !== 'numeric') {
                $formData[$field] = null;
            }
        }
    }
}
