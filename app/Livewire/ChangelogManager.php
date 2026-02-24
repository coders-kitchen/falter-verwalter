<?php

namespace App\Livewire;

use App\Models\ChangelogEntry;
use Livewire\Component;
use Livewire\WithPagination;

class ChangelogManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $audienceFilter = 'all';
    public string $statusFilter = 'all';

    public bool $showModal = false;
    public ?ChangelogEntry $entry = null;

    public array $form = [
        'version' => '',
        'title' => '',
        'summary' => '',
        'details' => '',
        'audience' => 'both',
        'published_at' => '',
        'is_active' => true,
        'commit_refs' => [
            ['url' => '', 'label' => ''],
        ],
    ];

    protected function rules(): array
    {
        $entryId = $this->entry?->id ?? 'NULL';

        return [
            'form.version' => 'required|string|max:50|unique:changelog_entries,version,' . $entryId,
            'form.title' => 'required|string|max:255',
            'form.summary' => 'required|string',
            'form.details' => 'nullable|string',
            'form.audience' => 'required|in:public,admin,both',
            'form.published_at' => 'required|date',
            'form.is_active' => 'boolean',
            'form.commit_refs' => 'array',
            'form.commit_refs.*.url' => ['nullable', 'url', 'regex:/^https:\/\/github\.com\/[\w.-]+\/[\w.-]+\/commit\/[0-9a-f]{7,40}$/i'],
            'form.commit_refs.*.label' => 'nullable|string|max:120',
        ];
    }

    protected function messages(): array
    {
        return [
            'form.version.required' => 'Bitte eine Version eingeben.',
            'form.version.unique' => 'Diese Version existiert bereits.',
            'form.title.required' => 'Bitte einen Titel eingeben.',
            'form.summary.required' => 'Bitte eine Zusammenfassung eingeben.',
            'form.audience.required' => 'Bitte eine Zielgruppe waehlen.',
            'form.published_at.required' => 'Bitte ein Veroeffentlichungsdatum setzen.',
            'form.commit_refs.*.url.regex' => 'Commit-Links muessen GitHub-Commit-URLs sein.',
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedAudienceFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->entry = null;
        $this->form = [
            'version' => '',
            'title' => '',
            'summary' => '',
            'details' => '',
            'audience' => 'both',
            'published_at' => now()->format('Y-m-d\\TH:i'),
            'is_active' => true,
            'commit_refs' => [
                ['url' => '', 'label' => ''],
            ],
        ];
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function openEditModal(ChangelogEntry $entry): void
    {
        $this->entry = $entry;
        $refs = collect($entry->commit_refs ?? [])->map(function ($row) {
            return [
                'url' => $row['url'] ?? '',
                'label' => $row['label'] ?? '',
            ];
        })->values()->all();

        if (empty($refs)) {
            $refs = [['url' => '', 'label' => '']];
        }

        $this->form = [
            'version' => $entry->version,
            'title' => $entry->title,
            'summary' => $entry->summary,
            'details' => $entry->details ?? '',
            'audience' => $entry->audience,
            'published_at' => optional($entry->published_at)->format('Y-m-d\\TH:i') ?? now()->format('Y-m-d\\TH:i'),
            'is_active' => (bool) $entry->is_active,
            'commit_refs' => $refs,
        ];

        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->entry = null;
        $this->resetErrorBag();
    }

    public function addCommitRef(): void
    {
        $this->form['commit_refs'][] = ['url' => '', 'label' => ''];
    }

    public function removeCommitRef(int $index): void
    {
        unset($this->form['commit_refs'][$index]);
        $this->form['commit_refs'] = array_values($this->form['commit_refs']);

        if (empty($this->form['commit_refs'])) {
            $this->form['commit_refs'][] = ['url' => '', 'label' => ''];
        }
    }

    public function save(): void
    {
        $this->validate();

        $commitRefs = collect($this->form['commit_refs'])
            ->filter(fn (array $row) => trim((string) ($row['url'] ?? '')) !== '')
            ->map(function (array $row) {
                preg_match('/commit\/([0-9a-f]{7,40})/i', (string) $row['url'], $matches);

                return [
                    'sha' => $matches[1] ?? null,
                    'url' => trim((string) $row['url']),
                    'label' => trim((string) ($row['label'] ?? '')) ?: null,
                ];
            })
            ->values()
            ->all();

        $payload = [
            'version' => trim($this->form['version']),
            'title' => trim($this->form['title']),
            'summary' => trim($this->form['summary']),
            'details' => trim((string) $this->form['details']) ?: null,
            'audience' => $this->form['audience'],
            'published_at' => $this->form['published_at'],
            'is_active' => (bool) $this->form['is_active'],
            'commit_refs' => $commitRefs,
        ];

        if ($this->entry) {
            $this->entry->update($payload);
            $this->dispatch('notify', message: 'Changelog-Eintrag aktualisiert.');
        } else {
            ChangelogEntry::create($payload);
            $this->dispatch('notify', message: 'Changelog-Eintrag erstellt.');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function delete(ChangelogEntry $entry): void
    {
        $entry->delete();
        $this->dispatch('notify', message: 'Changelog-Eintrag geloescht.');
        $this->resetPage();
    }

    public function render()
    {
        $query = ChangelogEntry::query()->orderByDesc('published_at')->orderByDesc('id');

        if (trim($this->search) !== '') {
            $search = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('version', 'like', $search)
                    ->orWhere('title', 'like', $search)
                    ->orWhere('summary', 'like', $search);
            });
        }

        if ($this->audienceFilter !== 'all') {
            $query->where('audience', $this->audienceFilter);
        }

        if ($this->statusFilter === 'active') {
            $query->where('is_active', true);
        }

        if ($this->statusFilter === 'inactive') {
            $query->where('is_active', false);
        }

        return view('livewire.changelog-manager', [
            'items' => $query->paginate(25),
        ]);
    }
}
