<div class="space-y-6">
    <div class="flex justify-between items-center flex-wrap gap-4">
        <h2 class="text-3xl font-bold">🌿 Pflanzen</h2>
        <button wire:click="openCreateModal" class="btn btn-primary">
            + Neue Pflanze
        </button>
    </div>

    <div class="form-control">
        <input
            wire:model.live="search"
            type="text"
            placeholder="Suche nach Pflanzennamen..."
            class="input input-bordered w-full"
        />
    </div>

    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Wissenschaftlicher Name</th>
                    <th>Lebensart</th>
                    <th>Höhe (cm)</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr class="hover">
                        <td class="font-semibold">{{ $item->name }}</td>
                        <td class="italic text-sm">{{ $item->scientific_name ?? '—' }}</td>
                        <td>{{ $item->lifeForm->name ?? '—' }}</td>
                        <td>{{ $item->plant_height_cm ?? '—' }}</td>
                        <td class="space-x-2">
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
                            Keine Pflanzen gefunden
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($items->hasPages())
        <div class="flex justify-center gap-2">
            {{ $items->links() }}
        </div>
    @endif

    @if($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-lg p-8 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <h3 class="text-2xl font-bold text-black dark:text-white mb-6">
                    {{ $plant ? 'Pflanze bearbeiten' : 'Neue Pflanze' }}
                </h3>

                <form wire:submit="save" class="space-y-4">
                    <!-- Basic Info -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Name *</span>
                            </label>
                            <input
                                wire:model="form.name"
                                type="text"
                                placeholder="z.B. Schneeglöckchen"
                                class="input input-bordered @error('form.name') input-error @enderror"
                            />
                            @error('form.name')
                                <span class="text-error text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Wissenschaftlicher Name</span>
                            </label>
                            <input
                                wire:model="form.scientific_name"
                                type="text"
                                placeholder="z.B. Galanthus"
                                class="input input-bordered"
                            />
                        </div>
                    </div>

                    <!-- Life Form and Attributes -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Lebensart *</span>
                            </label>
                            <select wire:model="form.life_form_id" class="select select-bordered @error('form.life_form_id') select-error @enderror">
                                <option value="">— Wählen Sie eine Lebensart —</option>
                                @foreach($lifeForms as $lf)
                                    <option value="{{ $lf->id }}">{{ $lf->name }}</option>
                                @endforeach
                            </select>
                            @error('form.life_form_id')
                                <span class="text-error text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Lifespan *</span>
                            </label>
                            <select wire:model="form.lifespan" class="select select-bordered">
                                <option value="annual">Einjährig</option>
                                <option value="biennial">Zweijährig</option>
                                <option value="perennial">Mehrjährig</option>
                            </select>
                        </div>
                    </div>

                    <!-- Ecological Scales (1-9) -->
                    <div class="divider my-4">Ökologische Zeigerwerte (1-9)</div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Lichtzahl</span>
                            </label>
                            <input
                                wire:model="form.light_number"
                                type="range"
                                min="1"
                                max="9"
                                class="range"
                            />
                            <div class="text-xs text-center mt-1">{{ $form['light_number'] }}</div>
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Temperaturzahl</span>
                            </label>
                            <input
                                wire:model="form.temperature_number"
                                type="range"
                                min="1"
                                max="9"
                                class="range"
                            />
                            <div class="text-xs text-center mt-1">{{ $form['temperature_number'] }}</div>
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Kontinentalitätszahl</span>
                            </label>
                            <input
                                wire:model="form.continentality_number"
                                type="range"
                                min="1"
                                max="9"
                                class="range"
                            />
                            <div class="text-xs text-center mt-1">{{ $form['continentality_number'] }}</div>
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Reaktionszahl</span>
                            </label>
                            <input
                                wire:model="form.reaction_number"
                                type="range"
                                min="1"
                                max="9"
                                class="range"
                            />
                            <div class="text-xs text-center mt-1">{{ $form['reaction_number'] }}</div>
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Feuchtezahl</span>
                            </label>
                            <input
                                wire:model="form.moisture_number"
                                type="range"
                                min="1"
                                max="9"
                                class="range"
                            />
                            <div class="text-xs text-center mt-1">{{ $form['moisture_number'] }}</div>
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Feuchtewechsel</span>
                            </label>
                            <input
                                wire:model="form.moisture_variation"
                                type="range"
                                min="1"
                                max="9"
                                class="range"
                            />
                            <div class="text-xs text-center mt-1">{{ $form['moisture_variation'] }}</div>
                        </div>

                        <div class="form-control col-span-3">
                            <label class="label">
                                <span class="label-text font-semibold">Stickstoffzahl</span>
                            </label>
                            <input
                                wire:model="form.nitrogen_number"
                                type="range"
                                min="1"
                                max="9"
                                class="range"
                            />
                            <div class="text-xs text-center mt-1">{{ $form['nitrogen_number'] }}</div>
                        </div>
                    </div>

                    <!-- Botanical Attributes -->
                    <div class="divider my-4">Botanische Merkmale</div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Blütenfarbe</span>
                            </label>
                            <input
                                wire:model="form.bloom_color"
                                type="text"
                                placeholder="z.B. Weiß, Rosa"
                                class="input input-bordered"
                            />
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Pflanzenhöhe (cm)</span>
                            </label>
                            <input
                                wire:model="form.plant_height_cm"
                                type="number"
                                min="0"
                                placeholder="z.B. 30"
                                class="input input-bordered"
                            />
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Standort</span>
                            </label>
                            <input
                                wire:model="form.location"
                                type="text"
                                placeholder="z.B. Schattig"
                                class="input input-bordered"
                            />
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Gefährdungsstatus</span>
                            </label>
                            <input
                                wire:model="form.threat_status"
                                type="text"
                                placeholder="z.B. Nicht gefährdet"
                                class="input input-bordered"
                            />
                        </div>
                    </div>

                    <!-- Flags -->
                    <div class="flex gap-4 my-4">
                        <div class="form-control">
                            <label class="label cursor-pointer">
                                <input
                                    wire:model="form.is_native"
                                    type="checkbox"
                                    class="checkbox checkbox-sm"
                                />
                                <span class="label-text ml-2">Heimisch</span>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer">
                                <input
                                    wire:model="form.is_invasive"
                                    type="checkbox"
                                    class="checkbox checkbox-sm"
                                />
                                <span class="label-text ml-2">Invasiv</span>
                            </label>
                        </div>
                    </div>

                    <!-- Habitats -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Lebensräume</span>
                        </label>
                        <div class="space-y-2 max-h-40 overflow-y-auto border border-gray-300 rounded p-3">
                            @foreach($habitats as $habitat)
                                <div class="form-control">
                                    <label class="label cursor-pointer">
                                        <input
                                            wire:model="form.habitat_ids"
                                            type="checkbox"
                                            value="{{ $habitat->id }}"
                                            class="checkbox checkbox-sm"
                                        />
                                        <span class="label-text ml-2">{{ $habitat->name }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

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
