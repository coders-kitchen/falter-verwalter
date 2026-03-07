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
                    <th>Gattung</th>
                    <th>Tags</th>
                    <th>Größe</th>
                    <th>Generationen</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr class="hover">
                        <td class="font-semibold">{{ $item->name }}</td>
                        <td>{{ $item->genus->name ?? '—' }}</td>
                        <td>
                            <div class="flex flex-wrap gap-1">
                                @forelse($item->tags as $tag)
                                    <span class="badge badge-outline badge-sm">{{ $tag->name }}</span>
                                @empty
                                    <span class="text-xs text-base-content/60">—</span>
                                @endforelse
                            </div>
                        </td>
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
                            <a
                                href="{{ route('admin.speciesPlants.index', $item->id) }}"
                                class="btn btn-xs btn-success"
                            >
                                Pflanzen
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
                        <td colspan="6" class="text-center py-8 text-gray-500">
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

                    <!-- Genus -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Gattung (mit Hierarchie) *</span>
                        </label>
                        <select wire:model="form.genus_id" class="select select-bordered @error('form.genus_id') select-error @enderror">
                            <option value="">— Wählen Sie eine Gattung —</option>
                            @foreach($genera as $genus)
                                <option value="{{ $genus['id'] }}">{{ $genus['label'] }}</option>
                            @endforeach
                        </select>
                        @error('form.genus_id')
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Phagie-Stufe (Adulte)</span>
                            </label>
                            <select wire:model="form.adult_phagy_level" class="select select-bordered @error('form.adult_phagy_level') select-error @enderror">
                                <option value="">— Keine Auswahl —</option>
                                <option value="unbekannt">Unbekannt</option>
                                <option value="monophag">Monophag</option>
                                <option value="oligophag">Oligophag</option>
                                <option value="polyphag">Polyphag</option>
                            </select>
                            @error('form.adult_phagy_level')
                                <span class="text-error text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Phagie-Stufe (Raupe)</span>
                            </label>
                            <select wire:model="form.larval_phagy_level" class="select select-bordered @error('form.larval_phagy_level') select-error @enderror">
                                <option value="">— Keine Auswahl —</option>
                                <option value="unbekannt">Unbekannt</option>
                                <option value="monophag">Monophag</option>
                                <option value="oligophag">Oligophag</option>
                                <option value="polyphag">Polyphag</option>
                            </select>
                            @error('form.larval_phagy_level')
                                <span class="text-error text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Besondere Merkmale</span>
                        </label>
                        <input
                            wire:model="form.special_features"
                            type="text"
                            placeholder="z.B. auffällige Zeichnung, besondere Färbung"
                            class="input input-bordered @error('form.special_features') input-error @enderror"
                        />
                        @error('form.special_features')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Tags</span>
                        </label>
                        <div class="space-y-2">
                            <div class="flex flex-wrap gap-2">
                                @forelse($selectedTags as $tag)
                                    <button
                                        type="button"
                                        class="badge badge-neutral gap-2"
                                        wire:click="removeTag({{ $tag->id }})"
                                        title="Tag entfernen"
                                    >
                                        {{ $tag->name }} <span>✕</span>
                                    </button>
                                @empty
                                    <span class="text-xs text-base-content/60">Noch keine Tags ausgewählt.</span>
                                @endforelse
                            </div>
                            <input
                                type="text"
                                wire:model.live.debounce.200ms="tagSearch"
                                class="input input-bordered"
                                placeholder="Tag suchen..."
                            >

                            @if($suggestedTags->count() > 0)
                                <div class="border border-base-300 rounded-lg max-h-44 overflow-y-auto">
                                    @foreach($suggestedTags as $tag)
                                        <button
                                            type="button"
                                            class="w-full text-left px-3 py-2 hover:bg-base-200 border-b border-base-300 last:border-b-0"
                                            wire:click="addTag({{ $tag->id }})"
                                        >
                                            <span class="font-medium">{{ $tag->name }}</span>
                                            @if(!empty($tag->description))
                                                <span class="text-xs text-base-content/70 block">{{ $tag->description }}</span>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
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
