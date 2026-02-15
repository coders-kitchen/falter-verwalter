<?php

namespace App\Livewire;

use App\Models\DistributionArea;
use Illuminate\Support\Str;
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
        'code' => '',
        'description' => '',
        'geometry_geojson' => '',
    ];

    protected function rules(): array
    {
        $distributionAreaId = $this->distributionArea?->id ?? 'NULL';

        return [
            'form.name' => 'required|string|max:255|unique:distribution_areas,name,' . $distributionAreaId,
            'form.code' => 'required|string|max:120|alpha_dash|unique:distribution_areas,code,' . $distributionAreaId,
            'form.description' => 'nullable|string',
            'form.geometry_geojson' => 'nullable|json',
        ];
    }

    public function render()
    {
        $query = DistributionArea::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%')
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
            'code' => $distributionArea->code,
            'description' => $distributionArea->description,
            'geometry_geojson' => $distributionArea->geometry_geojson ? json_encode($distributionArea->geometry_geojson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '',
        ];
        $this->showModal = true;
    }

    public function save()
    {
        if (blank($this->form['code'] ?? null) && !blank($this->form['name'] ?? null)) {
            $this->form['code'] = Str::slug((string) $this->form['name']);
        }

        $validated = $this->validate();
        $payload = $validated['form'];
        $payload['geometry_geojson'] = blank($payload['geometry_geojson']) ? null : json_decode($payload['geometry_geojson'], true);

        if ($this->distributionArea) {
            $this->distributionArea->update($payload);
        } else {
            DistributionArea::create(array_merge($payload, ['user_id' => auth()->id()]));
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
            'code' => '',
            'description' => '',
            'geometry_geojson' => '',
        ];
        $this->resetErrorBag();
    }
}
