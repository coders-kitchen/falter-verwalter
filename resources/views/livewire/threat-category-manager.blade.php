<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center flex-wrap gap-4">
        <h2 class="text-3xl font-bold">Gefährdungskategorien</h2>
        <button wire:click="openCreateModal" class="btn btn-primary">
            + Neue Kategorie
        </button>
    </div>

    <!-- Search -->
    <div class="form-control">
        <input
            wire:model.live="search"
            type="text"
            placeholder="Suche nach Code oder Label..."
            class="input input-bordered w-full"
        />
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Label</th>
                    <th>Beschreibung</th>
                    <th>Farbcode</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr class="hover">
                        <td class="font-semibold">{{ $item->code }}</td>
                        <td>{{ $item->label }}</td>
                        <td class="text-sm">{{ $item->description ? substr($item->description, 0, 50) . '...' : '—' }}</td>
                        <td class="text-sm"><span class="badge badge-info badge-lg" style="background: {{ $item->color_code }};">{{ $item->color_code }}</span></td>
                        <td class="space-x-2">
                            <button
                                wire:click="openEditModal({{ $item->id }})"
                                class="btn btn-xs btn-info"
                            >
                                Bearbeiten
                            </button>
                            <button
                                wire:click="delete({{ $item->id }})"
                                wire:confirm="Wirklich löschen?"
                                class="btn btn-xs btn-error"
                            >
                                Löschen
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-8 text-gray-500">
                            Keine Gefährdungskategorien gefunden
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($items->hasPages())
        <div class="flex justify-center gap-2">
            {{ $items->links() }}
        </div>
    @endif

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-black/25 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-neutral-800 rounded-lg p-8 max-w-2xl w-full max-h-96 overflow-y-auto shadow-lg">
                <h3 class="text-2xl font-bold text-black dark:text-white mb-6">
                    {{ $threatCategory ? 'Kategorie bearbeiten' : 'Neue Kategorie erstellen' }}
                </h3>

                <form wire:submit="save" class="space-y-4">
                    <!-- Code -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Code *</span>
                        </label>
                        <input
                            wire:model="form.code"
                            type="text"
                            placeholder="z.B. NT"
                            class="input input-bordered @error('form.code') input-error @enderror"
                        />
                        @error('form.code')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Name -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Label *</span>
                        </label>
                        <input
                            wire:model="form.label"
                            type="text"
                            placeholder="z.B. Nicht gefährdet"
                            class="input input-bordered @error('form.name') input-error @enderror"
                        />
                        @error('form.label')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Beschreibung (optional)</span>
                        </label>
                        <textarea
                            wire:model="form.description"
                            class="textarea textarea-bordered"
                            placeholder="Kurze Beschreibung dieser Kategorie..."
                            rows="3"
                        ></textarea>
                    </div>


                    <!-- Rank -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Rang</span>
                        </label>
                        <input
                            type="number"
                            min="0"
                            wire:model="form.rank"
                            class="input input-bordered"
                            placeholder="z.B. 1, 2, 3">
                        @error('form.rank')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Farbcode</span>
                        </label>
                        <input type="color" value="#cfcfcf" class="input input-bordered" wire:model="form.color_code">
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-4 justify-end mt-8">
                        <button type="button" wire:click="closeModal" class="btn btn-ghost">
                            Abbrechen
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Speichern
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
