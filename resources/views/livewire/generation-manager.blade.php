<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold">Generationen: {{ $species->name }}</h2>
            <p class="text-sm text-base-content/60">{{ $species->scientific_name }}</p>
        </div>
        <button
            wire:click="openCreateModal"
            class="btn btn-primary">
            <span class="text-lg">+</span> Neue Generation
        </button>
    </div>

    <!-- Table -->
    @if($generations->count() > 0)
        <div class="overflow-x-auto">
            <table class="table table-sm table-zebra w-full">
                <thead>
                    <tr class="bg-base-200">
                        <th>Generation</th>
                        <th>Raupenzeit</th>
                        <th>Flugzeit</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($generations as $generation)
                        <tr>
                            <td class="font-semibold">{{ $generation->generation_number }}. Generation</td>
                            <td>
                                <div class="text-sm">
                                    {{ \App\Models\Generation::getMonthName($generation->larva_start_month) }}
                                    - {{ \App\Models\Generation::getMonthName($generation->larva_end_month) }}
                                </div>
                            </td>
                            <td>
                                <div class="text-sm">
                                    {{ \App\Models\Generation::getMonthName($generation->flight_start_month) }}
                                    - {{ \App\Models\Generation::getMonthName($generation->flight_end_month) }}
                                </div>
                            </td>
                            <td>
                                <div class="flex gap-2">
                                    <button
                                        wire:click="openEditModal({{ $generation->id }})"
                                        class="btn btn-ghost btn-xs">
                                        Bearbeiten
                                    </button>
                                    <button
                                        wire:click="delete({{ $generation->id }})"
                                        wire:confirm="Soll diese Generation wirklich gelöscht werden?"
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
            {{ $generations->links() }}
        </div>
    @else
        <div class="alert alert-info">
            <span>Keine Generationen erstellt. Klicken Sie auf "Neue Generation", um zu beginnen.</span>
        </div>
    @endif

    <!-- Modal -->
    @if($showModal)
        <div class="modal modal-open">
            <div class="modal-box w-11/12 max-w-2xl max-h-96 overflow-y-auto">
                <h3 class="text-lg font-bold mb-4">
                    {{ $generation ? $generation->generation_number . '. Generation bearbeiten' : 'Neue Generation erstellen' }}
                </h3>

                <div class="space-y-4">
                    <!-- Larva Period -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Raupenzeit Start</span>
                            </label>
                            <select wire:model="form.larva_start_month" class="select select-bordered">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}">{{ \App\Models\Generation::getMonthName($m) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Raupenzeit Ende</span>
                            </label>
                            <select wire:model="form.larva_end_month" class="select select-bordered">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}">{{ \App\Models\Generation::getMonthName($m) }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <!-- Flight Period -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Flugzeit Start</span>
                            </label>
                            <select wire:model="form.flight_start_month" class="select select-bordered">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}">{{ \App\Models\Generation::getMonthName($m) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Flugzeit Ende</span>
                            </label>
                            <select wire:model="form.flight_end_month" class="select select-bordered">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}">{{ \App\Models\Generation::getMonthName($m) }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Beschreibung (optional)</span>
                        </label>
                        <textarea
                            wire:model="form.description"
                            class="textarea textarea-bordered"
                            rows="2"
                            placeholder="z.B. Besonderheiten dieser Generation..."></textarea>
                    </div>
                    <div class="alert alert-info">
                        <span>Die Zuordnung von Nektar- und Futterpflanzen erfolgt jetzt artbezogen im Bereich "Pflanzen".</span>
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
