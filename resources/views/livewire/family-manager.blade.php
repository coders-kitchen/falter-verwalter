<div class="space-y-6">
    <div class="flex justify-between items-center flex-wrap gap-4">
        <h2 class="text-3xl font-bold">👨‍👩‍👧 Familien</h2>
        <button wire:click="openCreateModal" class="btn btn-primary">
            + Neue Familie
        </button>
    </div>

    <!-- Type Filter Tabs -->
    <div class="tabs tabs-bordered">
        <button
            wire:click="switchType('butterfly')"
            @class([
                'tab',
                'tab-active' => $filterType === 'butterfly',
            ])
        >
            🦋 Schmetterlinge
        </button>
        <button
            wire:click="switchType('plant')"
            @class([
                'tab',
                'tab-active' => $filterType === 'plant',
            ])
        >
            🌿 Pflanzen
        </button>
    </div>

    <!-- Search -->
    <div class="form-control">
        <input
            wire:model.live="search"
            type="text"
            placeholder="Suche nach Familie..."
            class="input input-bordered w-full"
        />
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>Familie</th>
                    <th>@if($filterType === 'butterfly')Arten@else Pflanzen @endif</th>
                    <th>Beschreibung</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr class="hover">
                        <td>
                            <div class="font-semibold">{{ $item->name }}</div>
                        </td>
                        <td>
                            @if($filterType === 'butterfly')
                                <span class="badge badge-primary">{{ $item->species_count }}</span>
                            @else
                                <span class="badge badge-success">{{ $item->plants_count }}</span>
                            @endif
                        </td>
                        <td class="text-sm">{{ $item->description ? substr($item->description, 0, 50) . '...' : '—' }}</td>
                        <td class="space-x-2">
                            <a
                                href="{{ route('admin.subfamilies.index', $item->id) }}"
                                class="btn btn-xs btn-success"
                            >
                                Unterfamilien
                            </a>
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
                        <td colspan="4" class="text-center py-8 text-gray-500">
                            Keine Familien gefunden
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($items->hasPages())
        <div class="flex justify-center gap-2">
            {{ $items->links() }}
        </div>
    @endif

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-black/25 flex items-center justify-center z-50">
            <div class="modal-box w-11/12 max-w-2xl max-h-96 overflow-y-auto">
                <h3 class="text-lg font-bold mb-4">
                    {{ $family ? 'Familie bearbeiten' : 'Neue Familie' }}
                </h3>

                <form wire:submit="save" class="space-y-4">
                    <!-- Type Selection -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Typ *</span>
                        </label>
                        <div class="flex gap-4">
                            <label class="label cursor-pointer gap-2">
                                <input
                                    type="radio"
                                    wire:model="form.type"
                                    value="butterfly"
                                    class="radio radio-sm"
                                />
                                <span class="label-text">🦋 Schmetterling</span>
                            </label>
                            <label class="label cursor-pointer gap-2">
                                <input
                                    type="radio"
                                    wire:model="form.type"
                                    value="plant"
                                    class="radio radio-sm"
                                />
                                <span class="label-text">🌿 Pflanze</span>
                            </label>
                        </div>
                    </div>

                    <!-- Familie (Name) -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Familie *</span>
                        </label>
                        <input
                            wire:model="form.name"
                            type="text"
                            placeholder="z.B. Papilionidae"
                            class="input input-bordered @error('form.name') input-error @enderror"
                        />
                        @error('form.name')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Beschreibung -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Beschreibung (optional)</span>
                        </label>
                        <textarea
                            wire:model="form.description"
                            class="textarea textarea-bordered"
                            placeholder="Kurze Beschreibung der Familie"
                            rows="3"
                        ></textarea>
                    </div>

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
