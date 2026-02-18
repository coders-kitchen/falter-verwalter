<?php

namespace App\Livewire;

use App\Models\ThreatCategory;
use Livewire\Component;
use Livewire\WithPagination;

class ThreatCategoryManager extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $threatCategory = null;
    public $form = [
        'code' => '',
        'label' => '',
        'rank' => 0,
        'description' => null,
        'color_code' => '#cfcfcf'
    ];

    protected function rules(): array
    {
        $threatCategoryId = $this->threatCategory?->id ?? 'NULL';

        return [
            'form.code' => 'required|string|max:20|unique:threat_categories,code,' . $threatCategoryId,
            'form.label' => 'nullable|string|max:40',
            'form.rank' => 'required|integer|min:0',
            'form.description' => 'nullable|string|max:256',
            'form.color_code' => 'required|string|min:7|max:7'
        ];
    }

    protected function messages(): array
    {
        return [
            'form.code.required' => 'Bitte einen Code eingeben.',
            'form.code.max' => 'Der Code darf maximal 20 Zeichen lang sein.',
            'form.code.unique' => 'Dieser Code ist bereits vergeben.',
            'form.label.max' => 'Das Label darf maximal 40 Zeichen lang sein.',
            'form.rank.required' => 'Bitte einen Rang angeben.',
            'form.rank.integer' => 'Der Rang muss eine ganze Zahl sein.',
            'form.rank.min' => 'Der Rang darf nicht negativ sein.',
            'form.description.max' => 'Die Beschreibung darf maximal 256 Zeichen lang sein.',
            'form.color_code.required' => 'Bitte einen Farbcode auswählen.',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.code' => 'Code',
            'form.label' => 'Label',
            'form.rank' => 'Rang',
            'form.description' => 'Beschreibung',
            'form.color_code' => 'Farbcode',
        ];
    }

    public function render()
    {
        $query = ThreatCategory::orderBy('rank');

        return view('livewire.threat-category-manager', [
            'items' => $query->paginate(50)
        ]);
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(ThreatCategory $threatCategory)
    {
        $this->threatCategory = $threatCategory;
        $this->form = $threatCategory->only('code', 'label', 'description', 'rank', 'color_code');

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

        $formData = $this->form;

        if ($this->threatCategory) {
            $this->threatCategory->update($formData);
            $this->dispatch('notify', message: 'Gefährdungsstatus aktualisiert');
        } else {
            $threatCategory = ThreatCategory::create(array_merge($formData, ['user_id' => auth()->id()]));
            $this->dispatch('notify', message: 'Gefährdungsstatus erstellt');
        }
        
        $this->closeModal();
        $this->resetPage();
    }

    public function delete(ThreatCategory $threatCategory)
    {
        $threatCategory->delete();
        $this->dispatch('notify', message: 'Gefährdungsstatus gelöscht');
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->form = [
        'code' => '',
        'label' => '',
        'rank' => 0,
        'description' => null,
        'color_code' => '#cfcfcf'
        ];
        $this->threatCategory = null;
    }
}
