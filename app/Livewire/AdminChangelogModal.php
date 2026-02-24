<?php

namespace App\Livewire;

use App\Models\ChangelogEntry;
use Illuminate\Support\Collection;
use Livewire\Component;

class AdminChangelogModal extends Component
{
    public bool $showModal = false;
    public Collection $entries;
    public ?string $latestVersion = null;

    public function mount(): void
    {
        $this->entries = collect();

        $user = auth()->user();

        if (!$user || !$user->isAdmin()) {
            return;
        }

        $visible = ChangelogEntry::query()
            ->active()
            ->published()
            ->forAdmin()
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        $latest = (clone $visible)->first();

        if (!$latest) {
            return;
        }

        $this->latestVersion = $latest->version;

        if ($this->hasUnseenUpdates($user->last_seen_changelog_version, $latest)) {
            $this->entries = $visible->take(3)->get();
            $this->showModal = true;
        }
    }

    public function dismiss(): void
    {
        $user = auth()->user();
        if ($user && $this->latestVersion) {
            $user->update(['last_seen_changelog_version' => $this->latestVersion]);
        }

        $this->showModal = false;
    }

    private function hasUnseenUpdates(?string $seenVersion, ChangelogEntry $latest): bool
    {
        if (!$seenVersion) {
            return true;
        }

        $seen = ChangelogEntry::query()->where('version', $seenVersion)->first();
        if (!$seen) {
            return true;
        }

        if ($latest->published_at->gt($seen->published_at)) {
            return true;
        }

        return $latest->published_at->equalTo($seen->published_at) && $latest->id > $seen->id;
    }

    public function render()
    {
        return view('livewire.admin-changelog-modal');
    }
}
