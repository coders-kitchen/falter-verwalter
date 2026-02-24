<?php

namespace App\Livewire\Public;

use App\Models\ChangelogEntry;
use Livewire\Component;
use Livewire\WithPagination;

class ChangelogPage extends Component
{
    use WithPagination;

    public function render()
    {
        $entries = ChangelogEntry::query()
            ->active()
            ->published()
            ->forPublic()
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(20);

        return view('livewire.public.changelog-page', [
            'entries' => $entries,
        ]);
    }
}
