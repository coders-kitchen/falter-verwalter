<?php

namespace App\Livewire;

use App\Models\Family;
use Livewire\Component;
use Livewire\WithPagination;

class FamilyManager extends Component
{
    use WithPagination;

    public $search = '';
    public $filterType = 'butterfly';
    public $showModal = false;
    public $family = null;

    public $form = [
        'name' => '',
        'subfamily' => '',
        'genus' => '',
        'tribe' => '',
        'type' => 'butterfly',
        'description' => '',
    ];

    protected $rules = [
        'form.name' => 'required|string|max:255',
        'form.subfamily' => 'nullable|string|max:255',
        'form.genus' => 'nullable|string|max:255',
        'form.tribe' => 'nullable|string|max:255',
        'form.type' => 'required|in:butterfly,plant',
        'form.description' => 'nullable|string',
    ];

    public function render()
    {
        $query = Family::withCount(['species', 'plants'])
            ->where('type', $this->filterType);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('subfamily', 'like', '%' . $this->search . '%')
                  ->orWhere('genus', 'like', '%' . $this->search . '%')
                  ->orWhere('tribe', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.family-manager', [
            'items' => $query->orderBy('name')->paginate(50),
        ]);
    }

    public function switchType($type)
    {
        $this->filterType = $type;
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->form['type'] = $this->filterType;
        $this->showModal = true;
    }

    public function openEditModal(Family $family)
    {
        $this->family = $family;
        $this->form = $family->only('name', 'subfamily', 'genus', 'tribe', 'type', 'description');
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function save()
    {
        // Build custom validation rules for hierarchical uniqueness
        $uniqueRule = 'unique:families,name,' . ($this->family?->id ?? 'NULL') . ',id,subfamily,' . ($this->form['subfamily'] ?? '') . ',genus,' . ($this->form['genus'] ?? '') . ',tribe,' . ($this->form['tribe'] ?? '') . ',type,' . $this->form['type'];

        $this->rules['form.name'] = 'required|string|max:255|' . $uniqueRule;

        $this->validate();

        if ($this->family) {
            $this->family->update($this->form);
        } else {
            Family::create(array_merge($this->form, ['user_id' => auth()->id()]));
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function delete(Family $family)
    {
        $speciesCount = $family->species()->count();
        $plantsCount = $family->plants()->count();

        if ($speciesCount > 0 || $plantsCount > 0) {
            $this->dispatch('notify', message: 'Kann nicht gelöscht werden: Es gibt noch zugeordnete Arten oder Pflanzen', type: 'error');
            return;
        }

        $family->delete();
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->form = [
            'name' => '',
            'subfamily' => '',
            'genus' => '',
            'tribe' => '',
            'type' => 'butterfly',
            'description' => '',
        ];
        $this->family = null;
        $this->resetErrorBag();
    }
}
