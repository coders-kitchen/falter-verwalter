<?php

namespace App\Livewire;

use App\Models\EndangeredRegion;
use Livewire\Component;
use Livewire\WithPagination;

class EndangeredRegionManager extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $endangeredRegion = null;

    public $form = [
        'code' => '',
        'name' => '',
        'description' => '',
    ];

    protected $rules = [
        'form.code' => 'required|string|max:20|unique:endangered_regions,code',
        'form.name' => 'required|string|max:255|unique:endangered_regions,name',
        'form.description' => 'nullable|string',
    ];

    public function render()
    {
        $query = EndangeredRegion::where('user_id', auth()->id())->orderBy('code');

        if ($this->search) {
            $query->where('code', 'like', '%' . $this->search . '%')
                  ->orWhere('name', 'like', '%' . $this->search . '%');
        }

        return view('livewire.endangered-region-manager', [
            'items' => $query->paginate(50),
        ]);
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(EndangeredRegion $endangeredRegion)
    {
        $this->endangeredRegion = $endangeredRegion;
        $this->form = $endangeredRegion->only('code', 'name', 'description');
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
        if ($this->endangeredRegion) {
            $this->rules['form.code'] = 'required|string|max:20|unique:endangered_regions,code,' . $this->endangeredRegion->id;
            $this->rules['form.name'] = 'required|string|max:255|unique:endangered_regions,name,' . $this->endangeredRegion->id;
        }

        $this->validate();

        if ($this->endangeredRegion) {
            $this->endangeredRegion->update($this->form);
            $this->dispatch('notify', message: 'Gefährdete Region aktualisiert');
        } else {
            EndangeredRegion::create(array_merge($this->form, ['user_id' => auth()->id()]));
            $this->dispatch('notify', message: 'Gefährdete Region erstellt');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function delete(EndangeredRegion $endangeredRegion)
    {
        $endangeredRegion->delete();
        $this->dispatch('notify', message: 'Gefährdete Region gelöscht');
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->form = [
            'code' => '',
            'name' => '',
            'description' => '',
        ];
        $this->endangeredRegion = null;
        $this->resetErrorBag();
    }
}
