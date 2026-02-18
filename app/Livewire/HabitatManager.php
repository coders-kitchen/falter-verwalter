<?php

namespace App\Livewire;

use App\Models\Habitat;
use Livewire\Component;
use Livewire\WithPagination;

class HabitatManager extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $habitat = null;

    public $form = [
        'name' => '',
        'description' => '',
        'parent_id' => null,
    ];

    protected $rules = [
        'form.name' => 'required|string|max:255',
        'form.description' => 'nullable|string',
        'form.parent_id' => 'nullable|exists:habitats,id',
    ];

    protected function messages(): array
    {
        return [
            'form.name.required' => 'Bitte einen Namen eingeben.',
            'form.name.max' => 'Der Name darf maximal 255 Zeichen lang sein.',
            'form.parent_id.exists' => 'Das ausgewählte übergeordnete Habitat ist ungültig.',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.name' => 'Name',
            'form.description' => 'Beschreibung',
            'form.parent_id' => 'Übergeordnetes Habitat',
        ];
    }

    public function render()
    {
        $query = Habitat::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
        }

        $items = $query->with('parent')
                       ->orderBy('name')
                       ->paginate(50);

        // Get all habitats for parent selector (excluding current habitat if editing)
        $allHabitats = Habitat::query()
            ->when($this->habitat, function ($q) {
                return $q->where('id', '!=', $this->habitat->id);
            })
            ->orderBy('name')
            ->get();

        return view('livewire.habitat-manager', [
            'items' => $items,
            'allHabitats' => $allHabitats,
        ]);
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(Habitat $habitat)
    {
        $this->habitat = $habitat;
        $this->form = [
            'name' => $habitat->name,
            'description' => $habitat->description,
            'parent_id' => $habitat->parent_id,
        ];
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->habitat) {
            // Prevent circular references
            if ($this->form['parent_id'] && $this->isCircularReference($this->habitat->id, $this->form['parent_id'])) {
                $this->addError('form.parent_id', 'Zirkulärer Verweis nicht erlaubt.');
                return;
            }

            $this->habitat->update($this->form);
        } else {
            Habitat::create(array_merge($this->form, ['user_id' => auth()->id()]));
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function delete(Habitat $habitat)
    {
        $habitat->delete();
        $this->resetPage();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->habitat = null;
        $this->form = [
            'name' => '',
            'description' => '',
            'parent_id' => null,
        ];
        $this->resetErrorBag();
    }

    /**
     * Check if setting parent_id would create a circular reference
     */
    private function isCircularReference($habitatId, $potentialParentId)
    {
        if ($potentialParentId === null) {
            return false;
        }

        $current = Habitat::find($potentialParentId);

        while ($current) {
            if ($current->id === $habitatId) {
                return true;
            }
            $current = $current->parent;
        }

        return false;
    }
}
