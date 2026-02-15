<div class="space-y-4">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold">Unterfamilien von: {{ $family->name }}</h2>
            <p class="text-sm text-base-content/60">Typ: {{ $family->type }}</p>
        </div>
        <button wire:click="openCreateModal" class="btn btn-primary">+ Neue Unterfamilie</button>
    </div>

    <div class="overflow-x-auto">
        <table class="table table-sm table-zebra w-full">
            <thead>
                <tr class="bg-base-200">
                    <th>Name</th>
                    <th>Triben</th>
                    <th>Gattungen</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subfamilies as $item)
                    <tr>
                        <td class="font-semibold">{{ $item->name }}</td>
                        <td>{{ $item->tribes_count }}</td>
                        <td>{{ $item->genera_count }}</td>
                        <td class="space-x-2">
                            <a href="{{ route('admin.tribes.index', $item->id) }}" class="btn btn-xs btn-success">Triben</a>
                            <a href="{{ route('admin.genera.index', $item->id) }}" class="btn btn-xs btn-success">Gattungen</a>
                            <button wire:click="openEditModal({{ $item->id }})" class="btn btn-xs btn-info">Bearbeiten</button>
                            <button wire:click="delete({{ $item->id }})" wire:confirm="Wirklich löschen?" class="btn btn-xs btn-error">Löschen</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-6 text-base-content/60">Keine Unterfamilien vorhanden.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex justify-center">{{ $subfamilies->links() }}</div>

    @if($showModal)
        <div class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg mb-4">{{ $subfamily ? 'Unterfamilie bearbeiten' : 'Neue Unterfamilie' }}</h3>
                <div class="form-control">
                    <label class="label"><span class="label-text">Name *</span></label>
                    <input wire:model="form.name" type="text" class="input input-bordered @error('form.name') input-error @enderror">
                    @error('form.name') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="modal-action">
                    <button wire:click="closeModal" class="btn">Abbrechen</button>
                    <button wire:click="save" class="btn btn-primary">Speichern</button>
                </div>
            </div>
            <div class="modal-backdrop" wire:click="closeModal"></div>
        </div>
    @endif
</div>
