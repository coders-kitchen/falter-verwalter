<?php

namespace App\Livewire;

use App\Models\Genus;
use App\Models\Subfamily;
use App\Models\Tribe;
use Livewire\Component;
use Livewire\WithPagination;

class GenusManager extends Component
{
    use WithPagination;

    public int $subfamily_id;
    public Subfamily $subfamily;
    public bool $showModal = false;
    public ?Genus $genus = null;

    public array $form = [
        'name' => '',
        'tribe_id' => '',
    ];

    protected $rules = [
        'form.name' => 'required|string|max:100',
        'form.tribe_id' => 'nullable|exists:tribes,id',
    ];

    protected function messages(): array
    {
        return [
            'form.name.required' => 'Bitte einen Namen eingeben.',
            'form.name.max' => 'Der Name darf maximal 100 Zeichen lang sein.',
            'form.tribe_id.exists' => 'Die ausgewählte Tribus ist ungültig.',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.name' => 'Name',
            'form.tribe_id' => 'Tribus',
        ];
    }

    public function mount($subfamilyId): void
    {
        $this->subfamily_id = (int) $subfamilyId;
        $this->subfamily = Subfamily::with('family')->findOrFail($subfamilyId);
    }

    public function render()
    {
        $genera = Genus::where('subfamily_id', $this->subfamily_id)
            ->with('tribe')
            ->orderBy('name')
            ->paginate(50);

        $tribes = Tribe::where('subfamily_id', $this->subfamily_id)
            ->orderBy('name')
            ->get();

        return view('livewire.genus-manager', [
            'genera' => $genera,
            'tribes' => $tribes,
        ]);
    }

    public function openCreateModal(): void
    {
        $this->genus = null;
        $this->form = ['name' => '', 'tribe_id' => ''];
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function openEditModal(Genus $genus): void
    {
        $this->genus = $genus;
        $this->form = [
            'name' => $genus->name,
            'tribe_id' => $genus->tribe_id ?? '',
        ];
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $tribeId = $this->form['tribe_id'] ? (int) $this->form['tribe_id'] : null;

        if ($tribeId) {
            $tribeBelongs = Tribe::where('id', $tribeId)
                ->where('subfamily_id', $this->subfamily_id)
                ->exists();

            if (!$tribeBelongs) {
                $this->addError('form.tribe_id', 'Die Tribus gehört nicht zur ausgewählten Unterfamilie.');
                return;
            }
        }

        $exists = Genus::where('subfamily_id', $this->subfamily_id)
            ->where('name', $this->form['name'])
            ->when($tribeId === null, fn ($q) => $q->whereNull('tribe_id'), fn ($q) => $q->where('tribe_id', $tribeId))
            ->when($this->genus, fn ($q) => $q->where('id', '!=', $this->genus->id))
            ->exists();

        if ($exists) {
            $this->addError('form.name', 'Diese Gattung existiert bereits mit gleicher Zuordnung.');
            return;
        }

        $payload = [
            'name' => $this->form['name'],
            'subfamily_id' => $this->subfamily_id,
            'tribe_id' => $tribeId,
        ];

        if ($this->genus) {
            $this->genus->update($payload);
        } else {
            Genus::create($payload);
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function delete(Genus $genus): void
    {
        if ($genus->species()->exists() || $genus->plants()->exists()) {
            $this->dispatch('notify', message: 'Kann nicht gelöscht werden: Es gibt noch zugeordnete Arten oder Pflanzen.', type: 'error');
            return;
        }

        $genus->delete();
        $this->resetPage();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->genus = null;
        $this->form = ['name' => '', 'tribe_id' => ''];
    }
}
