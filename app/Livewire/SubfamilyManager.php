<?php

namespace App\Livewire;

use App\Models\Family;
use App\Models\Subfamily;
use Livewire\Component;
use Livewire\WithPagination;

class SubfamilyManager extends Component
{
    use WithPagination;

    public int $family_id;
    public Family $family;
    public bool $showModal = false;
    public ?Subfamily $subfamily = null;

    public array $form = [
        'name' => '',
    ];

    protected $rules = [
        'form.name' => 'required|string|max:100',
    ];

    protected function messages(): array
    {
        return [
            'form.name.required' => 'Bitte einen Namen eingeben.',
            'form.name.max' => 'Der Name darf maximal 100 Zeichen lang sein.',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.name' => 'Name',
        ];
    }

    public function mount($familyId): void
    {
        $this->family_id = (int) $familyId;
        $this->family = Family::findOrFail($familyId);
    }

    public function render()
    {
        $subfamilies = Subfamily::where('family_id', $this->family_id)
            ->withCount(['tribes', 'genera'])
            ->orderBy('name')
            ->paginate(50);

        return view('livewire.subfamily-manager', [
            'subfamilies' => $subfamilies,
        ]);
    }

    public function openCreateModal(): void
    {
        $this->subfamily = null;
        $this->form = ['name' => ''];
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function openEditModal(Subfamily $subfamily): void
    {
        $this->subfamily = $subfamily;
        $this->form = ['name' => $subfamily->name];
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $exists = Subfamily::where('family_id', $this->family_id)
            ->where('name', $this->form['name'])
            ->when($this->subfamily, fn ($q) => $q->where('id', '!=', $this->subfamily->id))
            ->exists();

        if ($exists) {
            $this->addError('form.name', 'Diese Unterfamilie existiert bereits.');
            return;
        }

        if ($this->subfamily) {
            $this->subfamily->update($this->form);
        } else {
            Subfamily::create([
                'family_id' => $this->family_id,
                'name' => $this->form['name'],
            ]);
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function delete(Subfamily $subfamily): void
    {
        $hasChildren = $subfamily->tribes()->exists() || $subfamily->genera()->exists();
        if ($hasChildren) {
            $this->dispatch('notify', message: 'Kann nicht gelöscht werden: Es gibt noch Triben oder Gattungen.', type: 'error');
            return;
        }

        $subfamily->delete();
        $this->resetPage();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->subfamily = null;
        $this->form = ['name' => ''];
    }
}
