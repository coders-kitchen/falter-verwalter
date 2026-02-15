<div class="space-y-4">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold">Pflanzenzuordnung von: {{ $species->name }}</h2>
            <p class="text-sm text-base-content/60">{{ $species->scientific_name }}</p>
        </div>
        <button wire:click="openCreateModal" class="btn btn-primary">
            <span class="text-lg">+</span> Neue Pflanzenzuordnung
        </button>
    </div>

    @if($speciesPlants->count() > 0)
        <div class="overflow-x-auto">
            <table class="table table-sm table-zebra w-full">
                <thead>
                    <tr class="bg-base-200">
                        <th>Pflanze</th>
                        <th>Nutzung</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($speciesPlants as $row)
                        <tr>
                            <td class="font-semibold">{{ $row->plant->name ?? '—' }}</td>
                            <td>
                                <div class="flex gap-2">
                                    @if($row->is_nectar)
                                        <span class="badge badge-info">🌺 Nektarpflanze</span>
                                    @endif
                                    @if($row->is_larval_host)
                                        <span class="badge badge-success">🥬 Futterpflanze</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="flex gap-2">
                                    <button wire:click="openEditModal({{ $row->id }})" class="btn btn-ghost btn-xs">Bearbeiten</button>
                                    <button wire:click="delete({{ $row->id }})" wire:confirm="Soll diese Pflanzenzuordnung wirklich gelöscht werden?" class="btn btn-error btn-xs">Löschen</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex justify-center">
            {{ $speciesPlants->links() }}
        </div>
    @else
        <div class="alert alert-info">
            <span>Noch keine Pflanzen zugeordnet. Klicken Sie auf "Neue Pflanzenzuordnung".</span>
        </div>
    @endif

    @if($showModal)
        <div class="modal modal-open">
            <div class="modal-box w-11/12 max-w-2xl max-h-96 overflow-y-auto">
                <h3 class="text-lg font-bold mb-4">
                    {{ $speciesPlant ? 'Pflanzenzuordnung bearbeiten' : 'Neue Pflanzenzuordnung erstellen' }}
                </h3>

                <div class="space-y-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Pflanze *</span>
                        </label>
                        <select wire:model="form.plant_id" class="select select-bordered @error('form.plant_id') select-error @enderror" @if($speciesPlant) disabled @endif>
                            <option value="">— Wählen Sie eine Pflanze —</option>
                            @if ($speciesPlant)
                                <option value="{{ $speciesPlant->plant_id }}">{{ $speciesPlant->plant->name ?? '—' }}</option>
                            @endif
                            @foreach($plants as $plant)
                                <option value="{{ $plant->id }}">{{ $plant->name }}</option>
                            @endforeach
                        </select>
                        @error('form.plant_id')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" class="checkbox" wire:model="form.is_nectar" />
                            <span class="label-text">🌺 Nektarpflanze (Adulte Falter)</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" class="checkbox" wire:model="form.is_larval_host" />
                            <span class="label-text">🥬 Futterpflanze (Raupen)</span>
                        </label>
                    </div>

                    @error('form.is_nectar')
                        <span class="text-error text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="modal-action mt-6">
                    <button wire:click="closeModal" class="btn">Abbrechen</button>
                    <button wire:click="save" class="btn btn-primary">Speichern</button>
                </div>
            </div>
            <div class="modal-backdrop" wire:click="closeModal"></div>
        </div>
    @endif
</div>
