<div>
    @if($showModal)
        <div class="fixed inset-0 bg-base-content/55 backdrop-blur-[1px] z-[100] flex items-center justify-center p-4">
            <div class="bg-base-100 border-2 border-base-content/25 rounded-2xl shadow-2xl w-full max-w-3xl max-h-[85vh] overflow-y-auto">
                <div class="p-6 border-b-2 border-base-300 bg-base-200/70">
                    <h3 class="text-2xl font-bold">Neu seit deinem letzten Besuch</h3>
                    <p class="text-sm text-base-content/70 mt-1">Hier sind die neuesten wichtigen Aenderungen.</p>
                </div>

                <div class="p-6 space-y-5 bg-base-100">
                    @foreach($entries as $entry)
                        <article class="border-2 border-base-300 rounded-xl p-5 bg-base-200/35 shadow-sm">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                                <h4 class="font-semibold text-lg">{{ $entry->title }}</h4>
                                <span class="text-xs text-base-content/60">{{ optional($entry->published_at)->format('d.m.Y H:i') }}</span>
                            </div>
                            <div class="mt-1 text-sm text-base-content/70">Version {{ $entry->version }}</div>
                            <p class="mt-3">{{ $entry->summary }}</p>

                        @php
                            $details = trim((string) ($entry->details ?? ''));
                            $publicPart = null;
                            $adminPart = null;
                            $sourcePart = null;

                            if (preg_match('/Public:\\s*(.+?)(?:\\nAdmin:|\\nQuelle:|$)/s', $details, $match)) {
                                $publicPart = trim($match[1]);
                            }

                            if (preg_match('/Admin:\\s*(.+?)(?:\\nPublic:|\\nQuelle:|$)/s', $details, $match)) {
                                $adminPart = trim($match[1]);
                            }

                            if (preg_match('/Quelle:\\s*(.+)$/s', $details, $match)) {
                                $sourcePart = trim($match[1]);
                            }
                        @endphp

                        @if($publicPart)
                            <div class="mt-4 p-4 rounded-lg border-2 border-info/60 bg-info/15 shadow-sm">
                                <div class="text-xs font-semibold uppercase tracking-wide text-info">User-Frontend</div>
                                <p class="text-sm mt-1">{{ $publicPart }}</p>
                            </div>
                        @endif

                        @if($adminPart)
                            <div class="mt-4 p-4 rounded-lg border-2 border-secondary/60 bg-secondary/15 shadow-sm">
                                <div class="text-xs font-semibold uppercase tracking-wide text-secondary">Admin-Frontend</div>
                                <p class="text-sm mt-1">{{ $adminPart }}</p>
                            </div>
                        @endif

                        @if(!$publicPart && !$adminPart && $details !== '')
                            <p class="mt-3 text-sm text-base-content/80 whitespace-pre-line">{{ $details }}</p>
                        @endif

                        @if($sourcePart)
                            <p class="mt-3 text-xs text-base-content/60">Quelle: {{ $sourcePart }}</p>
                        @endif

                            @if(!empty($entry->commit_refs))
                                <div class="mt-4 space-y-1 p-3 rounded-lg border border-base-300 bg-base-100/80">
                                    <p class="text-xs uppercase tracking-wide text-base-content/60">Commits</p>
                                    @foreach($entry->commit_refs as $ref)
                                        <a href="{{ $ref['url'] ?? '#' }}" target="_blank" rel="noopener" class="link link-primary text-sm block">
                                            {{ $ref['label'] ?? ($ref['sha'] ?? 'Commit') }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>

                <div class="p-6 border-t-2 border-base-300 bg-base-200/70 flex justify-between items-center">
                    <a href="{{ route('admin.changelog.index') }}" class="link link-primary">Zum gesamten Changelog</a>
                    <button wire:click="dismiss" class="btn btn-primary">Verstanden</button>
                </div>
            </div>
        </div>
    @endif
</div>
