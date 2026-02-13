<?php

namespace App\Livewire;

use App\Models\ThreatCategory;
use App\Models\Family;
use App\Models\Habitat;
use App\Models\Region;
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

    protected $rules = [
        'form.code' => 'required|string|max:20',
        'form.label' => 'nullable|string|max:40',
        'form.rank' => 'required|integer|min:0',
        'form.description' => 'nullable|string|max:256',
        'form.color_code' => 'required|string|min:7|max:7'
    ];

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
            $threatCategory = threatCategory::create(array_merge($formData, ['user_id' => auth()->id()]));
            $this->dispatch('notify', message: 'Gefährdungsstatus erstellt');
        }
        
        $this->closeModal();
        $this->resetPage();
    }

    public function delete(threatCategory $threatCategory)
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
        'label' => '',
        'rank' => 0,
        'description' => null,
        'color_code' => '#cfcfcf'
        ];
        $this->threatCategory = null;
    }
}
