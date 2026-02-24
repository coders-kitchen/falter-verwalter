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
            <span class="text-lg">+</span> Neue Zuordnung
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
                        placeholder="Pflanzenname, wissenschaftlicher Name oder Gattung"
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

            @if($speciesAssignments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table table-sm table-zebra w-full">
                        <thead>
                            <tr>
                                <th>Typ</th>
                                <th>Name</th>
                                <th>Nutzung</th>
                                <th>Präferenz</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($speciesAssignments as $row)
                                <tr>
                                    <td>
                                        @if($row['type'] === 'genus')
                                            <span class="badge badge-outline">Gattung</span>
                                        @else
                                            <span class="badge badge-neutral">Art</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="font-semibold">{{ $row['name'] }}</div>
                                        @if(!empty($row['subtitle']))
                                            <div class="text-xs text-base-content/70">{{ $row['subtitle'] }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="flex gap-2">
                                            @if($row['is_nectar'])
                                                <span class="badge badge-info">🌺 Nektarpflanze</span>
                                            @endif
                                            @if($row['is_larval_host'])
                                                <span class="badge badge-success">🥬 Futterpflanze</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="space-y-1 text-sm">
                                            @if($row['is_nectar'])
                                                <div>
                                                    <span class="font-medium">Adulte:</span>
                                                    {{ $row['adult_preference'] === 'sekundaer' ? 'Sekundär' : ($row['adult_preference'] === 'primaer' ? 'Primär' : 'nicht gesetzt') }}
                                                </div>
                                            @endif
                                            @if($row['is_larval_host'])
                                                <div>
                                                    <span class="font-medium">Raupe:</span>
                                                    {{ $row['larval_preference'] === 'sekundaer' ? 'Sekundär' : ($row['larval_preference'] === 'primaer' ? 'Primär' : 'nicht gesetzt') }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex gap-2">
                                            @if($row['type'] === 'genus')
                                                <button wire:click="openEditGenusModal({{ $row['id'] }})" class="btn btn-ghost btn-xs">Bearbeiten</button>
                                                <button wire:click="deleteGenus({{ $row['id'] }})" wire:confirm="Soll diese Gattungszuordnung wirklich gelöscht werden?" class="btn btn-error btn-xs">Löschen</button>
                                            @else
                                                <button wire:click="openEditPlantModal({{ $row['id'] }})" class="btn btn-ghost btn-xs">Bearbeiten</button>
                                                <button wire:click="deletePlant({{ $row['id'] }})" wire:confirm="Soll diese Pflanzenzuordnung wirklich gelöscht werden?" class="btn btn-error btn-xs">Löschen</button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-center">
                    {{ $speciesAssignments->links(data: ['scrollTo' => false]) }}
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
                    @if($speciesPlant)
                        Pflanzenzuordnung bearbeiten
                    @elseif($speciesGenus)
                        Gattungszuordnung bearbeiten
                    @else
                        Neue Zuordnung anlegen
                    @endif
                </h3>

                <div class="space-y-4">
                    @if(!$speciesPlant && !$speciesGenus)
                        <div class="form-control">
                            <label class="label"><span class="label-text">Zuordnungstyp</span></label>
                            <div class="join w-full">
                                <button
                                    type="button"
                                    class="btn join-item flex-1 {{ $assignmentType === 'plant' ? 'btn-primary' : 'btn-outline' }}"
                                    wire:click="$set('assignmentType', 'plant')"
                                >
                                    Art
                                </button>
                                <button
                                    type="button"
                                    class="btn join-item flex-1 {{ $assignmentType === 'genus' ? 'btn-primary' : 'btn-outline' }}"
                                    wire:click="$set('assignmentType', 'genus')"
                                >
                                    Gattung
                                </button>
                            </div>
                        </div>
                    @endif

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" class="checkbox" wire:model.live="form.is_nectar" />
                            <span class="label-text">🌺 Nektarpflanze (Adulte Falter)</span>
                        </label>
                    </div>

                    @if($form['is_nectar'])
                        <div class="form-control">
                            <label class="label"><span class="label-text">Präferenz (Adulte)</span></label>
                            <select class="select select-bordered" wire:model.live="form.adult_preference">
                                <option value="primaer">Primär</option>
                                <option value="sekundaer">Sekundär</option>
                            </select>
                        </div>
                    @endif

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" class="checkbox" wire:model.live="form.is_larval_host" />
                            <span class="label-text">🥬 Futterpflanze (Raupen)</span>
                        </label>
                    </div>

                    @if($form['is_larval_host'])
                        <div class="form-control">
                            <label class="label"><span class="label-text">Präferenz (Raupe)</span></label>
                            <select class="select select-bordered" wire:model.live="form.larval_preference">
                                <option value="primaer">Primär</option>
                                <option value="sekundaer">Sekundär</option>
                            </select>
                        </div>
                    @endif

                    @error('form.is_nectar')
                        <span class="text-error text-sm">{{ $message }}</span>
                    @enderror
                    @error('form.adult_preference')
                        <span class="text-error text-sm">{{ $message }}</span>
                    @enderror
                    @error('form.larval_preference')
                        <span class="text-error text-sm">{{ $message }}</span>
                    @enderror

                    @if($speciesPlant)
                        <div class="alert alert-info">
                            <span>Bearbeite: {{ $speciesPlant->plant->name ?? '—' }}</span>
                        </div>
                    @elseif($speciesGenus)
                        <div class="alert alert-info">
                            <span>Bearbeite: {{ ($speciesGenus->genus->name ?? '—') . ' (sp.)' }}</span>
                        </div>
                    @else
                        <div class="divider">
                            {{ $assignmentType === 'genus' ? 'Gattungen auswählen (Mehrfachauswahl möglich)' : 'Pflanzen auswählen (Mehrfachauswahl möglich)' }}
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Suche in nicht zugeordneten {{ $assignmentType === 'genus' ? 'Gattungen' : 'Pflanzen' }}</span></label>
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="addSearch"
                                class="input input-bordered"
                                placeholder="{{ $assignmentType === 'genus' ? 'Gattungsname, Unterfamilie, Tribus oder Familie' : 'Pflanzenname, wissenschaftlicher Name oder Gattung' }}"
                            >
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <button wire:click="selectAllOnAddPage" class="btn btn-sm btn-outline">Alle auf Seite markieren</button>
                            <button wire:click="clearAddSelection" class="btn btn-sm btn-ghost">Markierung löschen</button>
                            <span class="badge badge-neutral">
                                {{ $assignmentType === 'genus' ? count($addSelectedGenusIds) : count($addSelectedPlantIds) }} markiert
                            </span>
                        </div>

                        <div class="overflow-x-auto border border-base-300 rounded">
                            <table class="table table-sm w-full">
                                <thead>
                                    <tr>
                                        <th class="w-10"></th>
                                        <th>{{ $assignmentType === 'genus' ? 'Gattung' : 'Pflanze' }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($addItems as $item)
                                        <tr>
                                            <td>
                                                @if($assignmentType === 'genus')
                                                    <input type="checkbox" class="checkbox checkbox-sm" value="{{ $item->id }}" wire:model="addSelectedGenusIds">
                                                @else
                                                    <input type="checkbox" class="checkbox checkbox-sm" value="{{ $item->id }}" wire:model="addSelectedPlantIds">
                                                @endif
                                            </td>
                                            <td>
                                                @if($assignmentType === 'genus')
                                                    <div class="font-semibold">{{ $item->name }} (sp.)</div>
                                                    <div class="text-xs text-base-content/70">{{ $item->displayLabel() }}</div>
                                                @else
                                                    <div class="font-semibold">{{ $item->name }}</div>
                                                    @if($item->scientific_name)
                                                        <div class="text-xs text-base-content/70">{{ $item->scientific_name }}</div>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center py-6 text-base-content/60">
                                                Keine passenden, noch nicht zugeordneten {{ $assignmentType === 'genus' ? 'Gattungen' : 'Pflanzen' }} gefunden.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($addItemsPagination)
                            <div class="flex justify-center">
                                {{ $addItemsPagination->links(data: ['scrollTo' => false]) }}
                            </div>
                        @endif

                        @error('selection')
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
