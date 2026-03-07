<div class="space-y-6">
    <div class="flex justify-between items-center flex-wrap gap-4">
        <h2 class="text-3xl font-bold">🏷️ Tags</h2>
        <button wire:click="openCreateModal" class="btn btn-primary">+ Neues Tag</button>
    </div>

    <div class="form-control">
        <input wire:model.live="search" type="text" placeholder="Tag suchen..." class="input input-bordered w-full" />
    </div>

    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Beschreibung</th>
                    <th>Status</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr class="hover">
                        <td class="font-semibold">{{ $item->name }}</td>
                        <td>{{ $item->description ?: '—' }}</td>
                        <td>
                            @if($item->is_active)
                                <span class="badge badge-success">aktiv</span>
                            @else
                                <span class="badge">inaktiv</span>
                            @endif
                        </td>
                        <td class="space-x-2">
                            <button wire:click="openEditModal({{ $item->id }})" class="btn btn-xs btn-info">Bearbeiten</button>
                            <button wire:click="delete({{ $item->id }})" wire:confirm="Wirklich löschen?" class="btn btn-xs btn-error">Löschen</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-8 text-gray-500">Keine Tags gefunden</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($items->hasPages())
        <div class="flex justify-center">{{ $items->links() }}</div>
    @endif

    @if($showModal)
        <div class="fixed inset-0 bg-black/25 flex items-center justify-center z-50 p-4">
            <div class="modal-box w-11/12 max-w-xl">
                <h3 class="text-lg font-bold mb-4">{{ $tag ? 'Tag bearbeiten' : 'Neues Tag' }}</h3>

                <form wire:submit="save" class="space-y-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">Name *</span></label>
                        <input wire:model="form.name" type="text" class="input input-bordered @error('form.name') input-error @enderror" />
                        @error('form.name')<span class="text-error text-sm mt-1">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">Beschreibung</span></label>
                        <textarea wire:model="form.description" rows="3" class="textarea textarea-bordered"></textarea>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" class="checkbox" wire:model="form.is_active" />
                            <span class="label-text">Aktiv</span>
                        </label>
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
