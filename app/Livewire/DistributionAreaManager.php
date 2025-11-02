<?php

namespace App\Livewire;

use App\Models\DistributionArea;
use Livewire\Component;
use Livewire\WithPagination;

class DistributionAreaManager extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $distributionArea = null;

    public $form = [
        'name' => '',
        'description' => '',
    ];

    protected $rules = [
        'form.name' => 'required|string|max:255|unique:distribution_areas,name',
        'form.description' => 'nullable|string',
    ];

    public function render()
    {
        $query = DistributionArea::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
        }

        $items = $query->orderBy('name')
                       ->paginate(50);

        return view('livewire.distribution-area-manager', [
            'items' => $items,
        ]);
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(DistributionArea $distributionArea)
    {
        $this->distributionArea = $distributionArea;
        $this->form = [
            'name' => $distributionArea->name,
            'description' => $distributionArea->description,
        ];
        $this->showModal = true;
    }

    public function save()
    {
        // Adjust unique rule for updates
        if ($this->distributionArea) {
            $this->rules['form.name'] = 'required|string|max:255|unique:distribution_areas,name,' . $this->distributionArea->id;
        }

        $this->validate();

        if ($this->distributionArea) {
            $this->distributionArea->update($this->form);
        } else {
            DistributionArea::create($this->form);
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function delete(DistributionArea $distributionArea)
    {
        $distributionArea->delete();
        $this->resetPage();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->distributionArea = null;
        $this->form = [
            'name' => '',
            'description' => '',
        ];
        $this->resetErrorBag();
    }
}
