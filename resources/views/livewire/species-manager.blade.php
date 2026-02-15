<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center flex-wrap gap-4">
        <h2 class="text-3xl font-bold">🦋 Schmetterlingsarten</h2>
        <button wire:click="openCreateModal" class="btn btn-primary" @can('create', \App\Models\Species::class)@endcan>
            + Neue Art
        </button>
    </div>

    <!-- Search -->
    <div class="form-control">
        <input
            wire:model.live="search"
            type="text"
            placeholder="Suche nach Name oder wissenschaftlichem Name..."
            class="input input-bordered w-full"
        />
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Familie</th>
                    <th>Größe</th>
                    <th>Generationen</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr class="hover">
                        <td class="font-semibold">{{ $item->name }}</td>
                        <td>{{ $item->family->name ?? '—' }}</td>
                        <td>
                            <span class="badge badge-lg">{{ $item->size_category }}</span>
                        </td>
                        <td>{{ $item->generations_per_year ?? '—' }}</td>
                        <td class="space-x-2">
                            <a
                                href="{{ route('admin.generations.index', $item->id) }}"
                                class="btn btn-xs btn-success"
                            >
                                Generationen
                            </a>
                            <a
                                href="{{ route('admin.speciesDistributionAreas.index', $item->id) }}"
                                class="btn btn-xs btn-success"
                            >
                                Verbreitungsgebiete
                            </a>
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
                            Keine Arten gefunden
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($items->hasPages())
        <div class="flex justify-center gap-2">
            {{ $items->links() }}
        </div>
    @endif

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-black/25 flex items-center justify-center z-50">
            <!--<div class="bg-white dark:bg-neutral-800 rounded-lg p-8 max-w-2xl w-full max-h-96 overflow-y-auto shadow-lg">-->
             <div class="modal-box w-11/12 max-w-2xl max-h-96 overflow-y-auto">
                <h3 class="text-lg font-bold mb-4">
                    {{ $species ? 'Art bearbeiten' : 'Neue Art erstellen' }}
                </h3>

                <form wire:submit="save" class="space-y-4">
                    <!-- Name -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Name *</span>
                        </label>
                        <input
                            wire:model="form.name"
                            type="text"
                            placeholder="z.B. Schwalbenschwanz"
                            class="input input-bordered @error('form.name') input-error @enderror"
                        />
                        @error('form.name')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Scientific Name -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Wissenschaftlicher Name</span>
                        </label>
                        <input
                            wire:model="form.scientific_name"
                            type="text"
                            placeholder="z.B. Papilio machaon"
                            class="input input-bordered"
                        />
                    </div>

                    <!-- Family -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Familie *</span>
                        </label>
                        <select wire:model="form.family_id" class="select select-bordered @error('form.family_id') select-error @enderror">
                            <option value="">— Wählen Sie eine Familie —</option>
                            @foreach($families as $family)
                                <option value="{{ $family->id }}">{{ $family->name }}</option>
                            @endforeach
                        </select>
                        @error('form.family_id')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Size Category -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Größenkategorie *</span>
                        </label>
                        <select wire:model="form.size_category" class="select select-bordered @error('form.size_category') select-error @enderror">
                            <option value="">— Wählen Sie eine Kategorie —</option>
                            <option value="XS">XS - sehr klein (&lt; 2,5 cm)</option>
                            <option value="S">S - klein (2,5 - 3,5 cm)</option>
                            <option value="M">M - mittelgroß (3,5 - 5 cm)</option>
                            <option value="L">L - groß (5 - 6,5 cm)</option>
                            <option value="XL">XL - sehr groß (≥ 6,5 cm)</option>
                        </select>
                        @error('form.size_category')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Generations per Year -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Generationen pro Jahr</span>
                        </label>
                        <input
                            wire:model="form.generations_per_year"
                            type="number"
                            min="1"
                            class="input input-bordered"
                        />
                    </div>

                    <!-- Hibernation Stage -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Überwinterungsstadium</span>
                        </label>
                        <select wire:model="form.hibernation_stage" class="select select-bordered">
                            <option value="">— Keine Auswahl —</option>
                            <option value="egg">Ei</option>
                            <option value="larva">Raupe</option>
                            <option value="pupa">Puppe</option>
                            <option value="adult">Imago (Schmetterling)</option>
                        </select>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Frisst auch Salbei-Arten</span>
                        </label>
                        <select wire:model="form.sage_feeding_indicator" class="select select-bordered @error('form.sage_feeding_indicator') select-error @enderror">
                            <option value="keine genaue Angabe">keine genaue Angabe</option>
                            <option value="Ja">Ja</option>
                            <option value="Nein">Nein</option>
                        </select>
                        @error('form.sage_feeding_indicator')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <!-- Habitats Multi-Select -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Lebensräume</span>
                        </label>
                        <select
                            wire:model="form.habitat_ids"
                            multiple
                            class="select select-bordered w-full"
                            size="8"
                        >
                            @foreach($habitats as $habitat)
                                <option value="{{ $habitat->id }}" style="padding-left: {{ ($habitat->level ?? 0) * 1.5 }}rem;">
                                    {{ str_repeat('— ', $habitat->level ?? 0) }}{{ $habitat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Buttons -->
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
