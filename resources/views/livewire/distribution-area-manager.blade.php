<div class="space-y-6">
    <div class="flex justify-between items-center flex-wrap gap-4">
        <h2 class="text-3xl font-bold">🌍 Verbreitungsgebiete</h2>
        <button wire:click="openCreateModal" class="btn btn-primary">
            + Neues Verbreitungsgebiet
        </button>
    </div>

    <div class="form-control">
        <input
            wire:model.live="search"
            type="text"
            placeholder="Suche nach Verbreitungsgebiet..."
            class="input input-bordered w-full"
        />
    </div>

    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Geometrie</th>
                    <th>Beschreibung</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr class="hover">
                        <td class="font-semibold">{{ $item->name }}</td>
                        <td><code>{{ $item->code }}</code></td>
                        <td>
                            @if ($item->geometry_geojson)
                                <span class="badge badge-success badge-sm">vorhanden</span>
                            @else
                                <span class="badge badge-ghost badge-sm">fehlt</span>
                            @endif
                        </td>
                        <td>{{ $item->description ?? '—' }}</td>
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
                            Keine Verbreitungsgebiete gefunden
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

    @if($showModal)
        <div class="fixed inset-0 bg-black/25 flex items-center justify-center z-50">
            <div class="modal-box w-11/12 max-w-2xl max-h-96 overflow-y-auto">
                <h3 class="text-lg font-bold mb-4">
                    {{ $distributionArea ? 'Verbreitungsgebiet bearbeiten' : 'Neues Verbreitungsgebiet' }}
                </h3>

                <form wire:submit="save" class="space-y-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Name *</span>
                        </label>
                        <input
                            wire:model="form.name"
                            type="text"
                            placeholder="z.B. Mitteleuropa"
                            class="input input-bordered @error('form.name') input-error @enderror"
                        />
                        @error('form.name')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Code *</span>
                        </label>
                        <input
                            wire:model="form.code"
                            type="text"
                            placeholder="z.B. bergisches-land"
                            class="input input-bordered @error('form.code') input-error @enderror"
                        />
                        <label class="label">
                            <span class="label-text-alt opacity-75">Stabile Kennung (nur Buchstaben, Zahlen, Bindestrich)</span>
                        </label>
                        @error('form.code')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Beschreibung</span>
                        </label>
                        <textarea
                            wire:model="form.description"
                            class="textarea textarea-bordered"
                            placeholder="Kurze Beschreibung des Verbreitungsgebiets"
                            rows="3"
                        ></textarea>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">GeoJSON-Geometrie</span>
                        </label>
                        <textarea
                            wire:model="form.geometry_geojson"
                            class="textarea textarea-bordered font-mono text-xs @error('form.geometry_geojson') textarea-error @enderror"
                            placeholder='{"type":"Polygon","coordinates":[...]}'
                            rows="8"
                        ></textarea>
                        <label class="label">
                            <span class="label-text-alt opacity-75">Erlaubt: GeoJSON Polygon oder MultiPolygon</span>
                        </label>
                        @error('form.geometry_geojson')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
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
