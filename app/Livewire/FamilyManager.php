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
        'type' => 'butterfly',
        'description' => '',
    ];

    protected $rules = [
        'form.name' => 'required|string|max:255',
        'form.type' => 'required|in:butterfly,plant',
        'form.description' => 'nullable|string',
    ];

    public function render()
    {
        $query = Family::withCount(['species', 'plants'])
            ->where('type', $this->filterType)
            ->whereNull('subfamily')
            ->whereNull('genus')
            ->whereNull('tribe');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
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
        $this->form = $family->only('name', 'type', 'description');
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

        $existsQuery = Family::where('type', $this->form['type'])
            ->where('name', $this->form['name'])
            ->whereNull('subfamily')
            ->whereNull('genus')
            ->whereNull('tribe');
        if ($this->family) {
            $existsQuery->where('id', '!=', $this->family->id);
        }
        if ($existsQuery->exists()) {
            $this->addError('form.name', 'Diese Familie existiert bereits.');
            return;
        }

        if ($this->family) {
            $this->family->update(array_merge($this->form, [
                'subfamily' => null,
                'genus' => null,
                'tribe' => null,
            ]));
        } else {
            Family::create(array_merge($this->form, [
                'user_id' => auth()->id(),
                'subfamily' => null,
                'genus' => null,
                'tribe' => null,
            ]));
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
            'type' => 'butterfly',
            'description' => '',
        ];
        $this->family = null;
        $this->resetErrorBag();
    }
}
