<div class="space-y-4">
    <div class="text-sm breadcrumbs">
        <ul>
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('admin.species.index') }}">Arten</a></li>
            <li>{{ $species->name }}</li>
            <li>Verbreitungsgebiete</li>
        </ul>
    </div>

    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold">Verbreitungsgebiete von : {{ $species->name }}</h2>
            <p class="text-sm text-base-content/60">{{ $species->scientific_name }}</p>
        </div>
        <button
            wire:click="openCreateModal"
            class="btn btn-primary">
            <span class="text-lg">+</span> Neues Verbreitungsgebiet
        </button>
    </div>

    <!-- Table -->
    @if($speciesDistributionAreas->count() > 0)
        <div class="overflow-x-auto">
            <table class="table table-sm table-zebra w-full">
                <thead>
                    <tr class="bg-base-200">
                        <th>Verbreitungsgebiet</th>
                        <th>Zustand</th>
                        <th>Gefährdungstatus</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($speciesDistributionAreas as $speciesAreaIterator)
                        <tr>
                            <td class="font-semibold">{{ $speciesAreaIterator->distributionArea->name }}</td>
                            <td class="font-semibold">{{ $speciesAreaIterator->status }}</td>
                            @if($speciesAreaIterator->threatCategory)
                            <td class="font-semibold">{{ $speciesAreaIterator->threatCategory->label }} ({{ $speciesAreaIterator->threatCategory->code }})</td>                    
                            @else
                            <td class="font-semibold">Kein Gefährundsstatus ausgewählt</td>                    
                            @endif
                            <td>
                                <div class="flex gap-2">
                                    <button
                                        wire:click="openEditModal({{ $speciesAreaIterator->id }})"
                                        class="btn btn-ghost btn-xs">
                                        Bearbeiten
                                    </button>
                                    <button
                                        wire:click="delete({{ $speciesAreaIterator->id }})"
                                        wire:confirm="Soll dieses Verbreitungsgebiet wirklich gelöscht werden?"
                                        class="btn btn-error btn-xs">
                                        Löschen
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            {{ $speciesDistributionAreas->links() }}
        </div>
    @else
        <div class="alert alert-info">
            <span>Keine Verbreitungsgebiet erstellt. Klicken Sie auf "Neues Verbreitungsgebiet", um zu beginnen.</span>
        </div>
    @endif

    <!-- Modal -->
    @if($showModal)
        <div class="modal modal-open">
            <div class="modal-box w-11/12 max-w-2xl max-h-96 overflow-y-auto">
                <h3 class="text-lg font-bold mb-4">
                    {{ $speciesArea ? 'Gebiet bearbeiten' : 'Neues Gebiet erstellen' }}
                </h3>

                <div class="space-y-4">
                    <!-- Distribution Area -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Gebiet *</span>
                        </label>
                        <select wire:model="form.distribution_area_id" class="select select-bordered @error('form.distribution_area_id') select-error @enderror">
                            <option value="">— Wählen Sie Gebiet —</option>
                            @if ($speciesArea)
                                <option value="{{ $speciesArea->distributionArea->id }}">{{ $speciesArea->distributionArea->name }}</option>
                            @endif
                            @foreach($distribution_areas as $distribution_area)
                                <option value="{{ $distribution_area->id }}">{{ $distribution_area->name }}</option>
                            @endforeach
                        </select>
                        @error('form.distribution_area_id')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Status *</span>
                        </label>
                        <select wire:model="form.status" class="select select-bordered select-sm flex-1">
                            <option value="heimisch">
                                heimisch
                            </option>
                            <option value="ausgestorben">
                                ausgestorben
                            </option>
                            <option value="neobiotisch">
                                neobiotisch
                            </option>
                        </select>
                    </div>
                    <!-- Threat Category -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Bedrohungsstatus</span>
                        </label>
                        <select wire:model="form.threat_category_id" class="select select-bordered @error('form.threat_category_id') select-error @enderror">
                            <option value="">— Wählen Sie einen Bedrohungsstatus —</option>
                            @foreach($threat_categories as $threat_category)
                                <option value="{{ $threat_category->id }}">{{ $threat_category->code }} ({{ $threat_category->label }})</option>
                            @endforeach
                        </select>
                        @error('form.threat_category_id')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="modal-action mt-6">
                    <button wire:click="closeModal" class="btn">Abbrechen</button>
                    <button wire:click="save" class="btn btn-primary">Speichern</button>
                </div>
            </div>
            <div class="modal-backdrop" wire:click="closeModal"></div>
        </div>
    @endif
</div>
