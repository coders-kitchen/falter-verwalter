<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center flex-wrap gap-4">
        <h2 class="text-3xl font-bold">Nutzer</h2>
        <button wire:click="openCreateModal" class="btn btn-primary">
            + Neuer Admin Nutzer
        </button>
    </div>

    <!-- Search -->
    <div class="form-control">
        <input
            wire:model.live="search"
            type="text"
            placeholder="Suche nach Code oder Label..."
            class="input input-bordered w-full"
        />
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>email</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr class="hover">
                        <td class="font-semibold">{{ $item->name }}</td>
                        <td>{{ $item->email }}</td>
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
                            Keine Admins gefunden
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
            <div class="bg-white dark:bg-neutral-800 rounded-lg p-8 max-w-2xl w-full max-h-96 overflow-y-auto shadow-lg">
                <h3 class="text-2xl font-bold text-black dark:text-white mb-6">
                    {{ $user ? 'Admin bearbeiten' : 'Neuen Admin erstellen' }}
                </h3>

                <form wire:submit="save" class="space-y-4">
                    <!-- Code -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Name *</span>
                        </label>
                        <input
                            wire:model="form.name"
                            type="text"
                            placeholder="z.B. NT"
                            class="input input-bordered @error('form.name') input-error @enderror"
                        />
                        @error('form.name')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Name -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">E-Mail *</span>
                        </label>
                        <input
                            wire:model="form.email"
                            type="text"
                            placeholder="z.B. Nicht gefährdet"
                            class="input input-bordered @error('form.email') input-error @enderror"
                        />
                        @error('form.email')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Name -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Passwort *</span>
                        </label>
                        <input
                            wire:model="form.password"
                            type="text"
                            class="input input-bordered @error('form.password') input-error @enderror"
                        />
                        @error('form.password')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
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
