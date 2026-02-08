<?php

namespace App\Livewire;

use App\Models\Region;
use Livewire\Component;
use Livewire\WithPagination;

class RegionManager extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $region = null;

    public $form = [
        'code' => '',
        'name' => '',
        'description' => '',
    ];

    protected $rules = [
        'form.code' => 'required|string|max:20|unique:regions,code',
        'form.name' => 'required|string|max:255|unique:regions,name',
        'form.description' => 'nullable|string',
    ];

    public function render()
    {
        $query = Region::orderBy('code');

        if ($this->search) {
            $query->where('code', 'like', '%' . $this->search . '%')
                  ->orWhere('name', 'like', '%' . $this->search . '%');
        }

        return view('livewire.region-manager', [
            'items' => $query->paginate(50),
        ]);
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(Region $region)
    {
        $this->region = $region;
        $this->form = $region->only('code', 'name', 'description');
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function save()
    {
        // Update validation to exclude current record from uniqueness check
        if ($this->region) {
            $this->rules['form.code'] = 'required|string|max:20|unique:regions,code,' . $this->region->id;
            $this->rules['form.name'] = 'required|string|max:255|unique:regions,name,' . $this->region->id;
        }

        $this->validate();

        if ($this->region) {
            $this->region->update($this->form);
            $this->dispatch('notify', message: 'Gefährdete Region aktualisiert');
        } else {
            Region::create(array_merge($this->form, ['user_id' => auth()->id()]));
            $this->dispatch('notify', message: 'Gefährdete Region erstellt');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function delete(Region $region)
    {
        $region->delete();
        $this->dispatch('notify', message: 'Region gelöscht');
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->form = [
            'code' => '',
            'name' => '',
            'description' => '',
        ];
        $this->region = null;
        $this->resetErrorBag();
    }
}
