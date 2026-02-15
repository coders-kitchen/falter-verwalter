<?php

namespace App\Livewire;

use App\Models\Generation;
use App\Models\Species;
use Livewire\Component;
use Livewire\WithPagination;

class GenerationManager extends Component
{
    use WithPagination;

    public $species_id;
    public $species = null;
    public $showModal = false;
    public $generation = null;
    public $search = '';

    public $form = [
        'generation_number' => 1,
        'larva_start_month' => 1,
        'larva_end_month' => 12,
        'flight_start_month' => 1,
        'flight_end_month' => 12,
        'description' => '',
    ];

    protected $rules = [
        'form.generation_number' => 'required|integer|min:1|max:12',
        'form.larva_start_month' => 'required|integer|between:1,12',
        'form.larva_end_month' => 'required|integer|between:1,12',
        'form.flight_start_month' => 'required|integer|between:1,12',
        'form.flight_end_month' => 'required|integer|between:1,12',
        'form.description' => 'nullable|string',
    ];

    public function mount($speciesId)
    {
        $this->species_id = $speciesId;
        $this->species = Species::findOrFail($speciesId);
    }

    public function render()
    {
        $generations = Generation::where('species_id', $this->species_id)
            ->orderBy('generation_number')
            ->paginate(20);

        return view('livewire.generation-manager', [
            'generations' => $generations,
        ]);
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(Generation $generation)
    {
        $this->generation = $generation;
        $this->form = [
            'generation_number' => $generation->generation_number,
            'larva_start_month' => $generation->larva_start_month,
            'larva_end_month' => $generation->larva_end_month,
            'flight_start_month' => $generation->flight_start_month,
            'flight_end_month' => $generation->flight_end_month,
            'description' => $generation->description ?? '',
        ];
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        // Ensure generation number is unique per species
        if (!$this->generation) {
            $existing = Generation::where('species_id', $this->species_id)
                ->where('generation_number', $this->form['generation_number'])
                ->first();

            if ($existing) {
                $this->addError('form.generation_number', 'Diese Generationsnummer existiert bereits für diese Art.');
                return;
            }
        } else {
            $existing = Generation::where('species_id', $this->species_id)
                ->where('generation_number', $this->form['generation_number'])
                ->where('id', '!=', $this->generation->id)
                ->first();

            if ($existing) {
                $this->addError('form.generation_number', 'Diese Generationsnummer existiert bereits für diese Art.');
                return;
            }
        }

        $formData = [
            'generation_number' => $this->form['generation_number'],
            'larva_start_month' => $this->form['larva_start_month'],
            'larva_end_month' => $this->form['larva_end_month'],
            'flight_start_month' => $this->form['flight_start_month'],
            'flight_end_month' => $this->form['flight_end_month'],
            'description' => $this->form['description'],
        ];

        if ($this->generation) {
            $this->generation->update($formData);
            $this->dispatch('notify', message: 'Gebiet aktualisiert');
        } else {
            Generation::create(array_merge($formData, [
                'user_id' => auth()->id(),
                'species_id' => $this->species_id,
            ]));
            $this->dispatch('notify', message: 'Gebiet zugewiesen');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function delete(Generation $generation)
    {
        $generation->delete();
        $this->resetPage();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->generation = null;
        $this->form = [
            'generation_number' => 1,
            'larva_start_month' => 1,
            'larva_end_month' => 12,
            'flight_start_month' => 1,
            'flight_end_month' => 12,
            'description' => '',
        ];
        $this->resetErrorBag();
    }
}
