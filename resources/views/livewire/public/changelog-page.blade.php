<div class="space-y-6">
    <h1 class="text-4xl font-bold">Was ist neu</h1>
    <p class="text-base-content/70">Aktuelle Verbesserungen und neue Funktionen in chronologischer Reihenfolge.</p>

    <div class="space-y-4">
        @forelse($entries as $entry)
            <article class="card bg-base-100 border border-base-300">
                <div class="card-body">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                        <h2 class="card-title text-2xl">{{ $entry->title }}</h2>
                        <div class="text-sm text-base-content/60">
                            {{ optional($entry->published_at)->format('d.m.Y H:i') }}
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <span class="badge badge-outline">Version {{ $entry->version }}</span>
                        @if($entry->audience === 'both')
                            <span class="badge badge-info">Public + Admin</span>
                        @elseif($entry->audience === 'public')
                            <span class="badge badge-info">Public</span>
                        @endif
                    </div>

                    <p class="leading-relaxed">{{ $entry->summary }}</p>

                    @if(!empty($entry->details))
                        <details class="mt-2">
                            <summary class="cursor-pointer font-medium">Details</summary>
                            <p class="mt-2 text-base-content/80 whitespace-pre-line">{{ $entry->details }}</p>
                        </details>
                    @endif
                </div>
            </article>
        @empty
            <div class="alert alert-info">
                <span>Noch keine Veroeffentlichungen vorhanden.</span>
            </div>
        @endforelse
    </div>

    @if($entries->hasPages())
        <div class="flex justify-center">
            {{ $entries->links(data: ['scrollTo' => false]) }}
        </div>
    @endif
</div>
