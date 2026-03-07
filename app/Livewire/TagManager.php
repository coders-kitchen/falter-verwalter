<?php

namespace App\Livewire;

use App\Models\Tag;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class TagManager extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?Tag $tag = null;

    public array $form = [
        'name' => '',
        'description' => '',
        'is_active' => true,
    ];

    protected function rules(): array
    {
        $id = $this->tag?->id ?? 'NULL';

        return [
            'form.name' => 'required|string|max:255|unique:tags,name,' . $id,
            'form.description' => 'nullable|string',
            'form.is_active' => 'boolean',
        ];
    }

    protected function messages(): array
    {
        return [
            'form.name.required' => 'Bitte einen Namen eingeben.',
            'form.name.unique' => 'Dieses Tag existiert bereits.',
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->tag = null;
        $this->form = [
            'name' => '',
            'description' => '',
            'is_active' => true,
        ];
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function openEditModal(Tag $tag): void
    {
        $this->tag = $tag;
        $this->form = [
            'name' => $tag->name,
            'description' => $tag->description ?? '',
            'is_active' => (bool) $tag->is_active,
        ];
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->tag = null;
        $this->resetErrorBag();
    }

    public function save(): void
    {
        $this->validate();

        $name = trim((string) $this->form['name']);

        $payload = [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => trim((string) ($this->form['description'] ?? '')) ?: null,
            'is_active' => (bool) $this->form['is_active'],
        ];

        if ($this->tag) {
            $this->tag->update($payload);
            $this->dispatch('notify', message: 'Tag aktualisiert.');
        } else {
            Tag::create($payload);
            $this->dispatch('notify', message: 'Tag erstellt.');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function delete(Tag $tag): void
    {
        $tag->delete();
        $this->dispatch('notify', message: 'Tag gelöscht.');
        $this->resetPage();
    }

    public function render()
    {
        $query = Tag::query()->orderBy('name');

        if (trim($this->search) !== '') {
            $search = '%' . trim($this->search) . '%';
            $query->where('name', 'like', $search)
                ->orWhere('description', 'like', $search);
        }

        return view('livewire.tag-manager', [
            'items' => $query->paginate(50),
        ]);
    }
}
