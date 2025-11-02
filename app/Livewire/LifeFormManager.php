<?php

namespace App\Livewire;

use App\Models\LifeForm;
use Livewire\Component;
use Livewire\WithPagination;

class LifeFormManager extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $lifeForm = null;

    public $form = [
        'name' => '',
        'description' => '',
        'examples' => '[]',
    ];

    protected $rules = [
        'form.name' => 'required|string|max:255|unique:life_forms,name',
        'form.description' => 'nullable|string',
        'form.examples' => 'nullable|json',
    ];

    public function render()
    {
        $query = LifeForm::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
        }

        $items = $query->orderBy('name')
                       ->paginate(50);

        return view('livewire.life-form-manager', [
            'items' => $items,
        ]);
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(LifeForm $lifeForm)
    {
        $this->lifeForm = $lifeForm;
        $examples = $lifeForm->examples ? json_decode($lifeForm->examples, true) : [];

        $this->form = [
            'name' => $lifeForm->name,
            'description' => $lifeForm->description,
            'examples' => json_encode($examples),
        ];

        $this->showModal = true;
    }

    public function save()
    {
        // Adjust unique rule for updates
        if ($this->lifeForm) {
            $this->rules['form.name'] = 'required|string|max:255|unique:life_forms,name,' . $this->lifeForm->id;
        }

        $this->validate();

        $formData = $this->form;

        // Decode examples JSON if provided
        if ($formData['examples']) {
            try {
                $formData['examples'] = json_encode(json_decode($formData['examples']));
            } catch (\Exception $e) {
                $formData['examples'] = null;
            }
        }

        if ($this->lifeForm) {
            $this->lifeForm->update($formData);
        } else {
            LifeForm::create($formData);
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function delete(LifeForm $lifeForm)
    {
        $lifeForm->delete();
        $this->resetPage();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->lifeForm = null;
        $this->form = [
            'name' => '',
            'description' => '',
            'examples' => '[]',
        ];
        $this->resetErrorBag();
    }
}
