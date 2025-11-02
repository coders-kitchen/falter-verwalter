<?php

namespace App\Livewire;

use App\Models\Family;
use Livewire\Component;
use Livewire\WithPagination;

class FamilyManager extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $family = null;
    public $form = ['name' => '', 'description' => ''];

    protected $rules = [
        'form.name' => 'required|string|max:255|unique:families,name',
        'form.description' => 'nullable|string',
    ];

    public function render()
    {
        $query = Family::withCount('species');

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        return view('livewire.family-manager', [
            'items' => $query->paginate(50),
        ]);
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(Family $family)
    {
        $this->family = $family;
        $this->form = $family->only('name', 'description');
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function save()
    {
        // Adjust rules for update
        if ($this->family) {
            $this->rules['form.name'] = 'required|string|max:255|unique:families,name,' . $this->family->id;
        }

        $this->validate();

        if ($this->family) {
            $this->family->update($this->form);
            $this->dispatch('notify', message: 'Familie aktualisiert');
        } else {
            Family::create(array_merge($this->form, ['user_id' => auth()->id()]));
            $this->dispatch('notify', message: 'Familie erstellt');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function delete(Family $family)
    {
        if ($family->species()->count() > 0) {
            $this->dispatch('notify', message: 'Kann nicht gelöscht werden: Es gibt noch zugeordnete Arten', type: 'error');
            return;
        }

        $family->delete();
        $this->dispatch('notify', message: 'Familie gelöscht');
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->form = ['name' => '', 'description' => ''];
        $this->family = null;
    }
}
