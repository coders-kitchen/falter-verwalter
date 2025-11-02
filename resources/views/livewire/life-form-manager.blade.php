<div class="space-y-6">
    <div class="flex justify-between items-center flex-wrap gap-4">
        <h2 class="text-3xl font-bold">🌱 Lebensarten</h2>
        <button wire:click="openCreateModal" class="btn btn-primary">
            + Neue Lebensart
        </button>
    </div>

    <div class="form-control">
        <input
            wire:model.live="search"
            type="text"
            placeholder="Suche nach Lebensart..."
            class="input input-bordered w-full"
        />
    </div>

    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Beschreibung</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr class="hover">
                        <td class="font-semibold">{{ $item->name }}</td>
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
                        <td colspan="3" class="text-center py-8 text-gray-500">
                            Keine Lebensarten gefunden
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
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-base-100 rounded-lg p-8 max-w-md w-full">
                <h3 class="text-2xl font-bold mb-6">
                    {{ $lifeForm ? 'Lebensart bearbeiten' : 'Neue Lebensart' }}
                </h3>

                <form wire:submit="save" class="space-y-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Name *</span>
                        </label>
                        <input
                            wire:model="form.name"
                            type="text"
                            placeholder="z.B. Baum"
                            class="input input-bordered @error('form.name') input-error @enderror"
                        />
                        @error('form.name')
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
                            placeholder="Kurze Beschreibung der Lebensart"
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
