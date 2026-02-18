<?php

namespace App\Livewire;

use App\Models\Subfamily;
use App\Models\Tribe;
use Livewire\Component;
use Livewire\WithPagination;

class TribeManager extends Component
{
    use WithPagination;

    public int $subfamily_id;
    public Subfamily $subfamily;
    public bool $showModal = false;
    public ?Tribe $tribe = null;

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

    public function mount($subfamilyId): void
    {
        $this->subfamily_id = (int) $subfamilyId;
        $this->subfamily = Subfamily::with('family')->findOrFail($subfamilyId);
    }

    public function render()
    {
        $tribes = Tribe::where('subfamily_id', $this->subfamily_id)
            ->withCount('genera')
            ->orderBy('name')
            ->paginate(50);

        return view('livewire.tribe-manager', [
            'tribes' => $tribes,
        ]);
    }

    public function openCreateModal(): void
    {
        $this->tribe = null;
        $this->form = ['name' => ''];
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function openEditModal(Tribe $tribe): void
    {
        $this->tribe = $tribe;
        $this->form = ['name' => $tribe->name];
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $exists = Tribe::where('subfamily_id', $this->subfamily_id)
            ->where('name', $this->form['name'])
            ->when($this->tribe, fn ($q) => $q->where('id', '!=', $this->tribe->id))
            ->exists();

        if ($exists) {
            $this->addError('form.name', 'Diese Tribus existiert bereits.');
            return;
        }

        if ($this->tribe) {
            $this->tribe->update($this->form);
        } else {
            Tribe::create([
                'subfamily_id' => $this->subfamily_id,
                'name' => $this->form['name'],
            ]);
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function delete(Tribe $tribe): void
    {
        if ($tribe->genera()->exists()) {
            $this->dispatch('notify', message: 'Kann nicht gelöscht werden: Es gibt noch zugeordnete Gattungen.', type: 'error');
            return;
        }

        $tribe->delete();
        $this->resetPage();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->tribe = null;
        $this->form = ['name' => ''];
    }
}
