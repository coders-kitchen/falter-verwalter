<div class="space-y-4">
    <div class="text-sm breadcrumbs">
        <ul>
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ ($subfamily->family->type ?? 'butterfly') === 'plant' ? route('admin.families.plants') : route('admin.families.butterflies') }}">Familien</a></li>
            <li><a href="{{ route('admin.subfamilies.index', $subfamily->family_id) }}">{{ $subfamily->family->name ?? 'Familie' }}</a></li>
            <li>{{ $subfamily->name }}</li>
            <li>Gattungen</li>
        </ul>
    </div>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold">Gattungen von: {{ $subfamily->name }}</h2>
            <p class="text-sm text-base-content/60">Familie: {{ $subfamily->family->name ?? '—' }}</p>
        </div>
        <button wire:click="openCreateModal" class="btn btn-primary">+ Neue Gattung</button>
    </div>

    <div class="overflow-x-auto">
        <table class="table table-sm table-zebra w-full">
            <thead>
                <tr class="bg-base-200">
                    <th>Name</th>
                    <th>Tribus</th>
                    <th>Pfad</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                @forelse($genera as $item)
                    <tr>
                        <td class="font-semibold">{{ $item->name }}</td>
                        <td>{{ $item->tribe->name ?? '—' }}</td>
                        <td class="text-sm text-base-content/70">{{ $item->hierarchyPath() }}</td>
                        <td class="space-x-2">
                            <button wire:click="openEditModal({{ $item->id }})" class="btn btn-xs btn-info">Bearbeiten</button>
                            <button wire:click="delete({{ $item->id }})" wire:confirm="Wirklich löschen?" class="btn btn-xs btn-error">Löschen</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-6 text-base-content/60">Keine Gattungen vorhanden.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex justify-center">{{ $genera->links() }}</div>

    @if($showModal)
        <div class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg mb-4">{{ $genus ? 'Gattung bearbeiten' : 'Neue Gattung' }}</h3>
                <div class="space-y-3">
                    <div class="form-control">
                        <label class="label"><span class="label-text">Name *</span></label>
                        <input wire:model="form.name" type="text" class="input input-bordered @error('form.name') input-error @enderror">
                        @error('form.name') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Tribus (optional)</span></label>
                        <select wire:model="form.tribe_id" class="select select-bordered @error('form.tribe_id') select-error @enderror">
                            <option value="">— Keine Tribus —</option>
                            @foreach($tribes as $tribe)
                                <option value="{{ $tribe->id }}">{{ $tribe->name }}</option>
                            @endforeach
                        </select>
                        @error('form.tribe_id') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
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
