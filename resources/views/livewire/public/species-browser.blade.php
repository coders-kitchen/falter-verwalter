<div class="space-y-6">
    <!-- Filters Section -->
    <div class="bg-base-200 p-6 rounded-lg space-y-4">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold">Filter</h3>
            <button
                wire:click="resetFilters"
                class="btn btn-sm btn-outline"
                title="Alle Filter zurücksetzen"
            >
                🔄 Zurücksetzen
            </button>
        </div>

        <!-- Search Input -->
        <div class="form-control">
            <label class="label">
                <span class="label-text font-semibold">🔍 Suche nach Name oder Code</span>
            </label>
            <input
                wire:model.live="search"
                type="text"
                placeholder="z.B. Tagpfauenauge, Monarchfalter..."
                class="input input-bordered w-full"
            />
        </div>

        <!-- Family Filter -->
        <div class="form-control">
            <label class="label">
                <span class="label-text font-semibold">Familie</span>
            </label>
            <select
                wire:model.live="familyId"
                class="select select-bordered w-full"
            >
                <option value="">Alle Familien</option>
                @foreach ($families as $family)
                    <option value="{{ $family->id }}">
                        {{ $family->code }} - {{ $family->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Habitats Multi-Select -->
        <div class="form-control">
            <label class="label">
                <span class="label-text font-semibold">🏞️ Lebensräume</span>
            </label>
            <select
                wire:model.live="habitatIds"
                multiple
                size="4"
                class="select select-bordered w-full"
            >
                @foreach ($habitats as $habitat)
                    <option value="{{ $habitat->id }}">
                        {{ str_repeat('— ', $habitat->level ?? 0) }}{{ $habitat->name }}
                    </option>
                @endforeach
            </select>
            <label class="label">
                <span class="label-text-alt text-xs opacity-75">Mehrere Lebensräume mit Ctrl/Cmd anwählen</span>
            </label>
        </div>

        <!-- new modelling with threat status and distribution areas -->
                <!-- Endangered Status -->
        <div class="form-control">
            <label class="label cursor-pointer gap-4">
                <span class="label-text font-semibold">⚠️ Gefährdungsstatus</span>
                <select
                    wire:model.live="threatCategoryId"
                    class="select select-sm select-bordered"
                >
                    <option value="">Alle</option>
                    @foreach ($threatCategories as $category)
                        <option value="{{ $category->id }}}">{{$category->code}} {{$category->label}}</option>
                    @endforeach
                    
                </select>
            </label>
        </div>

        <!-- Regions Multi-Select -->
        <div class="form-control">
            <label class="label">
                <span class="label-text font-semibold">📍 Regionen</span>
            </label>
            <select
                wire:model.live="distributionAreaIds"
                multiple
                size="4"
                class="select select-bordered w-full"
            >
                @foreach ($distributionAreas as $area)
                    <option value="{{ $area->id }}">
                        {{ $area->name }}
                    </option>
                @endforeach
            </select>
            <label class="label">
                <span class="label-text-alt text-xs opacity-75">Mehrere Regionen mit Ctrl/Cmd anwählen</span>
            </label>
        </div>
    </div>

    <!-- Results Section -->
    <div class="space-y-4">
        <h3 class="text-lg font-semibold">
            {{ $species->total() }} Arten gefunden
        </h3>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="table w-full table-sm md:table-md">
                <thead>
                    <tr class="bg-base-200">
                        <th>Code</th>
                        <th>Name</th>
                        <th>Familie</th>
                        <th>Beschreibung</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($species as $item)
                        <tr class="hover">
                            <td class="font-mono font-semibold text-sm">{{ $item->code }}</td>
                            <td class="font-semibold">{{ $item->name }}</td>
                            <td class="text-sm">
                                @if ($item->family)
                                    <span class="badge badge-sm">{{ $item->family->code }}</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="text-sm opacity-75 max-w-xs">
                                @if ($item->description)
                                    {{ substr($item->description, 0, 40) }}...
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td>
                                <a
                                    href="{{ route('species.show', $item) }}"
                                    class="btn btn-xs btn-primary"
                                    title="Art ansehen"
                                >
                                    👁️ Ansehen
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-gray-500">
                                <div class="space-y-4">
                                    <p class="text-lg font-semibold">Keine Arten gefunden</p>
                                    <p class="text-sm opacity-75">Versuchen Sie, die Filter anzupassen.</p>
                                    <button
                                        wire:click="resetFilters"
                                        class="btn btn-sm btn-outline"
                                    >
                                        Filter zurücksetzen
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($species->hasPages())
            <div class="flex justify-center py-6">
                {{ $species->links(data: ['scrollTo' => false]) }}
            </div>
        @endif
    </div>
</div>
