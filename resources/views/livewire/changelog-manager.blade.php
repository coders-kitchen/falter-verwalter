<div class="space-y-6">
    <div class="flex justify-between items-center flex-wrap gap-4">
        <h2 class="text-3xl font-bold">Changelog</h2>
        <button wire:click="openCreateModal" class="btn btn-primary">+ Neuer Eintrag</button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="form-control md:col-span-1">
            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Suche nach Version, Titel, Inhalt..."
                class="input input-bordered w-full"
            />
        </div>

        <div class="form-control">
            <select wire:model.live="audienceFilter" class="select select-bordered w-full">
                <option value="all">Alle Zielgruppen</option>
                <option value="both">Beide</option>
                <option value="public">Public</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <div class="form-control">
            <select wire:model.live="statusFilter" class="select select-bordered w-full">
                <option value="all">Alle Stati</option>
                <option value="active">Aktiv</option>
                <option value="inactive">Inaktiv</option>
            </select>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="table table-sm md:table-md w-full">
            <thead>
                <tr>
                    <th>Version</th>
                    <th>Titel</th>
                    <th>Zielgruppe</th>
                    <th>Status</th>
                    <th>Veroeffentlicht</th>
                    <th>Commits</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr class="hover">
                        <td class="font-mono text-xs">{{ $item->version }}</td>
                        <td>
                            <div class="font-semibold">{{ $item->title }}</div>
                            <div class="text-xs text-base-content/70">{{ \Illuminate\Support\Str::limit($item->summary, 100) }}</div>
                            @php
                                $details = (string) ($item->details ?? '');
                                $hasPublicPart = str_contains($details, 'Public:') || in_array($item->audience, ['public', 'both'], true);
                                $hasAdminPart = str_contains($details, 'Admin:') || in_array($item->audience, ['admin', 'both'], true);
                            @endphp
                            <div class="flex flex-wrap gap-1 mt-2">
                                @if($hasPublicPart)
                                    <span class="badge badge-info badge-sm">User-Frontend</span>
                                @endif
                                @if($hasAdminPart)
                                    <span class="badge badge-secondary badge-sm">Admin-Frontend</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-outline">{{ $item->audience }}</span>
                        </td>
                        <td>
                            @if($item->is_active)
                                <span class="badge badge-success">aktiv</span>
                            @else
                                <span class="badge">inaktiv</span>
                            @endif
                        </td>
                        <td class="text-sm">{{ optional($item->published_at)->format('d.m.Y H:i') }}</td>
                        <td>
                            @if(!empty($item->commit_refs))
                                <div class="flex flex-col gap-1">
                                    @foreach($item->commit_refs as $ref)
                                        <a href="{{ $ref['url'] ?? '#' }}" target="_blank" rel="noopener" class="link link-primary text-xs">
                                            {{ $ref['label'] ?? ($ref['sha'] ?? 'Commit') }}
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-sm text-base-content/60">—</span>
                            @endif
                        </td>
                        <td class="space-x-2 whitespace-nowrap">
                            <button wire:click="openEditModal({{ $item->id }})" class="btn btn-xs btn-info">Bearbeiten</button>
                            <button wire:click="delete({{ $item->id }})" wire:confirm="Eintrag wirklich loeschen?" class="btn btn-xs btn-error">Loeschen</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-base-content/70">Keine Eintraege gefunden.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($items->hasPages())
        <div class="flex justify-center">
            {{ $items->links(data: ['scrollTo' => false]) }}
        </div>
    @endif

    @if($showModal)
        <div class="fixed inset-0 bg-black/25 flex items-center justify-center z-50 p-4">
            <div class="modal-box w-11/12 max-w-4xl max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-bold mb-4">{{ $entry ? 'Changelog-Eintrag bearbeiten' : 'Neuen Changelog-Eintrag erstellen' }}</h3>

                <form wire:submit="save" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label"><span class="label-text">Version *</span></label>
                            <input type="text" wire:model="form.version" class="input input-bordered @error('form.version') input-error @enderror" placeholder="z.B. 2026.02.24.3">
                            @error('form.version')<span class="text-error text-sm">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Veroeffentlicht am *</span></label>
                            <input type="datetime-local" wire:model="form.published_at" class="input input-bordered @error('form.published_at') input-error @enderror">
                            @error('form.published_at')<span class="text-error text-sm">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Titel *</span></label>
                        <input type="text" wire:model="form.title" class="input input-bordered @error('form.title') input-error @enderror">
                        @error('form.title')<span class="text-error text-sm">{{ $message }}</span>@enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label"><span class="label-text">Zielgruppe *</span></label>
                            <select wire:model="form.audience" class="select select-bordered">
                                <option value="both">Beide</option>
                                <option value="public">Public</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3 mt-8">
                                <input type="checkbox" class="checkbox" wire:model="form.is_active">
                                <span class="label-text">Aktiv</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Zusammenfassung *</span></label>
                        <textarea wire:model="form.summary" rows="3" class="textarea textarea-bordered @error('form.summary') textarea-error @enderror"></textarea>
                        @error('form.summary')<span class="text-error text-sm">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Details (optional)</span></label>
                        <textarea wire:model="form.details" rows="5" class="textarea textarea-bordered"></textarea>
                        <div class="label">
                            <span class="label-text-alt text-base-content/70">Tipp: Mit `Public:` und `Admin:` kannst du die Bereiche optisch getrennt darstellen.</span>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h4 class="font-semibold">GitHub Commit-Links (optional)</h4>
                            <button type="button" wire:click="addCommitRef" class="btn btn-xs btn-outline">+ Link</button>
                        </div>

                        @foreach($form['commit_refs'] as $index => $ref)
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-end">
                                <div class="md:col-span-5 form-control">
                                    <label class="label"><span class="label-text text-xs">Label</span></label>
                                    <input type="text" wire:model="form.commit_refs.{{ $index }}.label" class="input input-bordered input-sm" placeholder="z.B. Pivot-Refactoring">
                                </div>
                                <div class="md:col-span-6 form-control">
                                    <label class="label"><span class="label-text text-xs">URL</span></label>
                                    <input type="url" wire:model="form.commit_refs.{{ $index }}.url" class="input input-bordered input-sm @error('form.commit_refs.' . $index . '.url') input-error @enderror" placeholder="https://github.com/org/repo/commit/...">
                                    @error('form.commit_refs.' . $index . '.url')<span class="text-error text-xs">{{ $message }}</span>@enderror
                                </div>
                                <div class="md:col-span-1">
                                    <button type="button" wire:click="removeCommitRef({{ $index }})" class="btn btn-sm btn-error btn-outline w-full">-</button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" wire:click="closeModal" class="btn btn-ghost">Abbrechen</button>
                        <button type="submit" class="btn btn-primary">Speichern</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
