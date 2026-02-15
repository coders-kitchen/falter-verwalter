<div class="space-y-4">
    <div class="text-sm breadcrumbs">
        <ul>
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('admin.species.index') }}">Arten</a></li>
            <li>{{ $species->name }}</li>
            <li>Pflanzenzuordnung</li>
        </ul>
    </div>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold">Pflanzenzuordnung von: {{ $species->name }}</h2>
            <p class="text-sm text-base-content/60">{{ $species->scientific_name }}</p>
        </div>
        <button wire:click="openCreateModal" class="btn btn-primary">
            <span class="text-lg">+</span> Neue Pflanzenzuordnung
        </button>
    </div>

    <div class="card bg-base-100 border border-base-300">
        <div class="card-body space-y-4">
            <h3 class="card-title">Aktuelle Zuordnungen</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="form-control">
                    <label class="label"><span class="label-text">Suche</span></label>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="assignedSearch"
                        class="input input-bordered"
                        placeholder="Pflanzenname oder wissenschaftlicher Name"
                    >
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Filter</span></label>
                    <select wire:model.live="assignedFilter" class="select select-bordered">
                        <option value="all">Alle</option>
                        <option value="nectar_only">Nur Nektar</option>
                        <option value="larval_only">Nur Futterpflanze</option>
                        <option value="both">Nektar + Futterpflanze</option>
                    </select>
                </div>
            </div>

            @if($speciesPlants->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table table-sm table-zebra w-full">
                        <thead>
                            <tr>
                                <th>Pflanze</th>
                                <th>Nutzung</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($speciesPlants as $row)
                                <tr>
                                    <td>
                                        <div class="font-semibold">{{ $row->plant->name ?? '—' }}</div>
                                        @if(!empty($row->plant?->scientific_name))
                                            <div class="text-xs text-base-content/70">{{ $row->plant->scientific_name }}</div>
                                        @endif
                                    </td>
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
                    <span>Keine Zuordnungen gefunden.</span>
                </div>
            @endif
        </div>
    </div>

    @if($showModal)
        <div class="modal modal-open">
            <div class="modal-box w-11/12 max-w-4xl max-h-[85vh] overflow-y-auto">
                <h3 class="text-lg font-bold mb-4">
                    {{ $speciesPlant ? 'Pflanzenzuordnung bearbeiten' : 'Neue Pflanzenzuordnung anlegen' }}
                </h3>

                <div class="space-y-4">
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

                    @if($speciesPlant)
                        <div class="alert alert-info">
                            <span>Bearbeite: {{ $speciesPlant->plant->name ?? '—' }}</span>
                        </div>
                    @else
                        <div class="divider">Pflanzen auswählen (Mehrfachauswahl möglich)</div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Suche in nicht zugeordneten Pflanzen</span></label>
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="addSearch"
                                class="input input-bordered"
                                placeholder="Pflanzenname oder wissenschaftlicher Name"
                            >
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <button wire:click="selectAllOnAddPage" class="btn btn-sm btn-outline">Alle auf Seite markieren</button>
                            <button wire:click="clearAddSelection" class="btn btn-sm btn-ghost">Markierung löschen</button>
                            <span class="badge badge-neutral">{{ count($addSelectedPlantIds) }} markiert</span>
                        </div>

                        <div class="overflow-x-auto border border-base-300 rounded">
                            <table class="table table-sm w-full">
                                <thead>
                                    <tr>
                                        <th class="w-10"></th>
                                        <th>Pflanze</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($addPlants as $plant)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="checkbox checkbox-sm" value="{{ $plant->id }}" wire:model="addSelectedPlantIds">
                                            </td>
                                            <td>
                                                <div class="font-semibold">{{ $plant->name }}</div>
                                                @if($plant->scientific_name)
                                                    <div class="text-xs text-base-content/70">{{ $plant->scientific_name }}</div>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center py-6 text-base-content/60">Keine passenden, noch nicht zugeordneten Pflanzen gefunden.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($addPlantsPagination)
                            <div class="flex justify-center">
                                {{ $addPlantsPagination->links() }}
                            </div>
                        @endif

                        @error('form.plant_id')
                            <span class="text-error text-sm">{{ $message }}</span>
                        @enderror
                    @endif
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
